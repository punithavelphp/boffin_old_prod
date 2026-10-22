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
use Mtwo\Coins\Api\Data\OrdersInterface;
use Mtwo\Coins\Api\Data\OrdersInterfaceFactory;
use Mtwo\Coins\Api\Data\OrdersSearchResultsInterfaceFactory;
use Mtwo\Coins\Api\OrdersRepositoryInterface;
use Mtwo\Coins\Model\ResourceModel\Orders as ResourceOrders;
use Mtwo\Coins\Model\ResourceModel\Orders\CollectionFactory as OrdersCollectionFactory;

class OrdersRepository implements OrdersRepositoryInterface
{

    /**
     * @var OrdersInterfaceFactory
     */
    protected $ordersFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Orders
     */
    protected $searchResultsFactory;

    /**
     * @var OrdersCollectionFactory
     */
    protected $ordersCollectionFactory;

    /**
     * @var ResourceOrders
     */
    protected $resource;


    /**
     * @param ResourceOrders $resource
     * @param OrdersInterfaceFactory $ordersFactory
     * @param OrdersCollectionFactory $ordersCollectionFactory
     * @param OrdersSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceOrders $resource,
        OrdersInterfaceFactory $ordersFactory,
        OrdersCollectionFactory $ordersCollectionFactory,
        OrdersSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->ordersFactory = $ordersFactory;
        $this->ordersCollectionFactory = $ordersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(OrdersInterface $orders)
    {
        try {
            $this->resource->save($orders);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the orders: %1',
                $exception->getMessage()
            ));
        }
        return $orders;
    }

    /**
     * @inheritDoc
     */
    public function get($ordersId)
    {
        $orders = $this->ordersFactory->create();
        $this->resource->load($orders, $ordersId);
        if (!$orders->getId()) {
            throw new NoSuchEntityException(__('orders with id "%1" does not exist.', $ordersId));
        }
        return $orders;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->ordersCollectionFactory->create();
        
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
    public function delete(OrdersInterface $orders)
    {
        try {
            $ordersModel = $this->ordersFactory->create();
            $this->resource->load($ordersModel, $orders->getOrdersId());
            $this->resource->delete($ordersModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the orders: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($ordersId)
    {
        return $this->delete($this->get($ordersId));
    }
}

