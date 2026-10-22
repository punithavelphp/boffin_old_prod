#!/bin/bash

echo "➡️ Checking if catalogsearch_fulltext index is locked..."

# Check if the status is stuck
locked=$(mysql -h 127.0.0.1 -N -e  "SELECT status FROM indexer_state WHERE indexer_id='catalogsearch_fulltext';"  -u boffinbu -pcwA1383n_ boffinbutl_live
)

if [[ "$locked" == "working" ]]; then
    echo "🔒 Index is locked. Resetting..."
    mysql -h 127.0.0.1 -N -e  "UPDATE indexer_state SET status = 'valid' WHERE indexer_id = 'catalogsearch_fulltext';"  -u boffinbu -pcwA1383n_ boffinbutl_live
else
    echo "✅ Index is not locked."
fi

echo "🧩 Collecting all product IDs..."
product_ids=$(mysql -h 127.0.0.1 -N -e  "SELECT entity_id FROM catalog_product_entity ORDER BY entity_id ASC;"  -u boffinbu -pcwA1383n_ boffinbutl_live
)

echo "📦 Starting batch indexing (1000 products per batch)..."

batch_size=10000
counter=0
ids_batch=""

for id in $product_ids; do
    ids_batch+="$id,"
    ((counter++))

    if (( counter % batch_size == 0 )); then
        ids_batch=${ids_batch%,} # remove trailing comma
        echo "🔄 Reindexing batch..."
        /opt/php81/bin/php -dmemory_limit=6G bin/magento indexer:reindex catalogsearch_fulltext
        ids_batch=""
    fi
done

# Index remaining products if any
if [ -n "$ids_batch" ]; then
    ids_batch=${ids_batch%,}
    echo "🔄 Reindexing final batch..."
    /opt/php81/bin/php bin/magento -dmemory_limit=6G indexer:reindex catalogsearch_fulltext
fi

echo "✅ Catalog Search Fulltext Indexing Completed!"
