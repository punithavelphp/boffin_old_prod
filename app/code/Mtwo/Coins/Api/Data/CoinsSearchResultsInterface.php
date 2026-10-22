<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api\Data;

interface CoinsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get coins list.
     * @return \Mtwo\Coins\Api\Data\CoinsInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Mtwo\Coins\Api\Data\CoinsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

