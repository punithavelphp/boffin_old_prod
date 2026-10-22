<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api\Data;

interface OrdersSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get orders list.
     * @return \Mtwo\Coins\Api\Data\OrdersInterface[]
     */
    public function getItems();

    /**
     * Set customer_name list.
     * @param \Mtwo\Coins\Api\Data\OrdersInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

