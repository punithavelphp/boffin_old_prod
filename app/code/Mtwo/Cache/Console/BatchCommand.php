<?php
namespace Mtwo\Cache\Console;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\State;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Processor as FulltextProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BatchCommand extends Command
{
    protected $state;
    protected $collectionFactory;
    protected $fulltextProcessor;

    public function __construct(
        State $state,
        CollectionFactory $collectionFactory,
        FulltextProcessor $fulltextProcessor
    ) {
        $this->state = $state;
        $this->collectionFactory = $collectionFactory;
        $this->fulltextProcessor = $fulltextProcessor;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('index:reindex:batch')
            ->setDescription('Reindex catalogsearch_fulltext in batches of 1000');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Already set
        }

        $batchSize = 10000;
        $currentPage = 1;

        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToSelect('entity_id');
        $productCollection->setPageSize($batchSize);

        $lastPage = $productCollection->getLastPageNumber();

        while ($currentPage <= $lastPage) {
            $productCollection->setCurPage($currentPage);
            $productCollection->clear(); // clear previous collection

$ids = $productCollection->getColumnValues('entity_id');

            if (!empty($ids)) {
                $output->writeln(" Reindexing batch page $currentPage with " . count($ids) . " products...");
               $this->fulltextProcessor->reindexList($ids);
            }

            $currentPage++;
        }

        $output->writeln(" Reindexing completed in batches.");
        return Command::SUCCESS;
    }
}