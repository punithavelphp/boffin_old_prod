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
use Mtwo\Coins\Api\CoinsRepositoryInterface;
use Mtwo\Coins\Api\Data\CoinsInterface;
use Mtwo\Coins\Api\Data\CoinsInterfaceFactory;
use Mtwo\Coins\Api\Data\CoinsSearchResultsInterfaceFactory;
use Mtwo\Coins\Model\ResourceModel\Coins as ResourceCoins;
use Mtwo\Coins\Model\ResourceModel\Coins\CollectionFactory as CoinsCollectionFactory;

class CoinsRepository implements CoinsRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ResourceCoins
     */
    protected $resource;

    /**
     * @var CoinsCollectionFactory
     */
    protected $coinsCollectionFactory;

    /**
     * @var CoinsInterfaceFactory
     */
    protected $coinsFactory;

    /**
     * @var Coins
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceCoins $resource
     * @param CoinsInterfaceFactory $coinsFactory
     * @param CoinsCollectionFactory $coinsCollectionFactory
     * @param CoinsSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceCoins $resource,
        CoinsInterfaceFactory $coinsFactory,
        CoinsCollectionFactory $coinsCollectionFactory,
        CoinsSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->coinsFactory = $coinsFactory;
        $this->coinsCollectionFactory = $coinsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(CoinsInterface $coins)
    {
        try {
            $this->resource->save($coins);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the coins: %1',
                $exception->getMessage()
            ));
        }
        return $coins;
    }

    /**
     * @inheritDoc
     */
    public function get($coinsId)
    {
        $coins = $this->coinsFactory->create();
        $this->resource->load($coins, $coinsId);
        if (!$coins->getId()) {
            throw new NoSuchEntityException(__('coins with id "%1" does not exist.', $coinsId));
        }
        return $coins;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->coinsCollectionFactory->create();
        
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
    public function delete(CoinsInterface $coins)
    {
        try {
            $coinsModel = $this->coinsFactory->create();
            $this->resource->load($coinsModel, $coins->getCoinsId());
            $this->resource->delete($coinsModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the coins: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($coinsId)
    {
        return $this->delete($this->get($coinsId));
    }
}

