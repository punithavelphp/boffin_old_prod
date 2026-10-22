<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api\Data;

interface CatalogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get catalog list.
     * @return \Mtwo\Coins\Api\Data\CatalogInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Mtwo\Coins\Api\Data\CatalogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

