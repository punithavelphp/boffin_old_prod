<?php

/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mtwo\Analytics\Api;

/**
 * Interface for managing customer groups.
 * @api
 * @since 100.0.2
 */
interface AnalyticsInterface {
 
	/**
     * Get  Department and institutes
     *
     * @return string $items
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

	public function getAnalytics();
	/**
     * Get  Department and institutes
     *
     * @param int $customerId
     * @return string $items
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

	public function getCustomerAnalytics($customerId);


}
