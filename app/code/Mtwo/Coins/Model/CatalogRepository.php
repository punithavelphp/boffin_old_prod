<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mtwo\Coins\Api\CatalogRepositoryInterface;
use Mtwo\Coins\Api\Data\CatalogInterface;
use Mtwo\Coins\Api\Data\CatalogInterfaceFactory;
use Mtwo\Coins\Api\Data\CatalogSearchResultsInterfaceFactory;
use Mtwo\Coins\Model\ResourceModel\Catalog as ResourceCatalog;
use Mtwo\Coins\Model\ResourceModel\Catalog\CollectionFactory as CatalogCollectionFactory;

class CatalogRepository implements CatalogRepositoryInterface
{

    /**
     * @var CatalogCollectionFactory
     */
    protected $catalogCollectionFactory;

    /**
     * @var ResourceCatalog
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Catalog
     */
    protected $searchResultsFactory;

    /**
     * @var CatalogInterfaceFactory
     */
    protected $catalogFactory;


    /**
     * @param ResourceCatalog $resource
     * @param CatalogInterfaceFactory $catalogFactory
     * @param CatalogCollectionFactory $catalogCollectionFactory
     * @param CatalogSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceCatalog $resource,
        CatalogInterfaceFactory $catalogFactory,
        CatalogCollectionFactory $catalogCollectionFactory,
        CatalogSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->catalogFactory = $catalogFactory;
        $this->catalogCollectionFactory = $catalogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(CatalogInterface $catalog)
    {
        try {
            $this->resource->save($catalog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the catalog: %1',
                $exception->getMessage()
            ));
        }
        return $catalog;
    }

    /**
     * @inheritDoc
     */
    public function get($catalogId)
    {
        $catalog = $this->catalogFactory->create();
        $this->resource->load($catalog, $catalogId);
        if (!$catalog->getId()) {
            throw new NoSuchEntityException(__('catalog with id "%1" does not exist.', $catalogId));
        }
        return $catalog;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->catalogCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(CatalogInterface $catalog)
    {
        try {
            $catalogModel = $this->catalogFactory->create();
            $this->resource->load($catalogModel, $catalog->getCatalogId());
            $this->resource->delete($catalogModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the catalog: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($catalogId)
    {
        return $this->delete($this->get($catalogId));
    }
}

