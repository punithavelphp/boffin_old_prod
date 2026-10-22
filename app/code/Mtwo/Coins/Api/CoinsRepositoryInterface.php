<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CoinsRepositoryInterface
{

    /**
     * Save coins
     * @param \Mtwo\Coins\Api\Data\CoinsInterface $coins
     * @return \Mtwo\Coins\Api\Data\CoinsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Mtwo\Coins\Api\Data\CoinsInterface $coins
    );

    /**
     * Retrieve coins
     * @param string $coinsId
     * @return \Mtwo\Coins\Api\Data\CoinsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($coinsId);

    /**
     * Retrieve coins matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mtwo\Coins\Api\Data\CoinsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete coins
     * @param \Mtwo\Coins\Api\Data\CoinsInterface $coins
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Mtwo\Coins\Api\Data\CoinsInterface $coins
    );

    /**
     * Delete coins by ID
     * @param string $coinsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($coinsId);
}

