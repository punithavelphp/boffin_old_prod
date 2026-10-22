<?php
declare(strict_types=1);

namespace Magelearn\Customform\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CustomformRepositoryInterface
{

    /**
     * Save Customform
     * @param \Magelearn\Customform\Api\Data\CustomformInterface $customform
     * @return \Magelearn\Customform\Api\Data\CustomformInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magelearn\Customform\Api\Data\CustomformInterface $customform
    );

    /**
     * Retrieve Customform
     * @param string $customformId
     * @return \Magelearn\Customform\Api\Data\CustomformInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($customformId);

    /**
     * Retrieve Customform matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magelearn\Customform\Api\Data\CustomformSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Customform
     * @param \Magelearn\Customform\Api\Data\CustomformInterface $customform
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magelearn\Customform\Api\Data\CustomformInterface $customform
    );

    /**
     * Delete Customform by ID
     * @param string $customformId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customformId);
}

