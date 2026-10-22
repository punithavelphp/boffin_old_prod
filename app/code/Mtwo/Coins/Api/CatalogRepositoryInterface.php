<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CatalogRepositoryInterface
{

    /**
     * Save catalog
     * @param \Mtwo\Coins\Api\Data\CatalogInterface $catalog
     * @return \Mtwo\Coins\Api\Data\CatalogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Mtwo\Coins\Api\Data\CatalogInterface $catalog
    );

    /**
     * Retrieve catalog
     * @param string $catalogId
     * @return \Mtwo\Coins\Api\Data\CatalogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($catalogId);

    /**
     * Retrieve catalog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mtwo\Coins\Api\Data\CatalogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete catalog
     * @param \Mtwo\Coins\Api\Data\CatalogInterface $catalog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Mtwo\Coins\Api\Data\CatalogInterface $catalog
    );

    /**
     * Delete catalog by ID
     * @param string $catalogId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($catalogId);
}

