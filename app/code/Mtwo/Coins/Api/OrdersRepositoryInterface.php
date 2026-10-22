<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface OrdersRepositoryInterface
{

    /**
     * Save orders
     * @param \Mtwo\Coins\Api\Data\OrdersInterface $orders
     * @return \Mtwo\Coins\Api\Data\OrdersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Mtwo\Coins\Api\Data\OrdersInterface $orders
    );

    /**
     * Retrieve orders
     * @param string $ordersId
     * @return \Mtwo\Coins\Api\Data\OrdersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($ordersId);

    /**
     * Retrieve orders matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mtwo\Coins\Api\Data\OrdersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete orders
     * @param \Mtwo\Coins\Api\Data\OrdersInterface $orders
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Mtwo\Coins\Api\Data\OrdersInterface $orders
    );

    /**
     * Delete orders by ID
     * @param string $ordersId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ordersId);
}

