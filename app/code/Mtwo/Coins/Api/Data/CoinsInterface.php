<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api\Data;

interface CoinsInterface
{

    const ORDER_ID = 'order_id';
    const COINS_ID = 'coins_id';
    const COINS = 'coins';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const IS_APPROVED = 'is_approved';
    const APPROVED_BY = 'approved_by';
    const CUSTOMER_ID = 'customer_id';

    /**
     * Get coins_id
     * @return string|null
     */
    public function getCoinsId();

    /**
     * Set coins_id
     * @param string $coinsId
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setCoinsId($coinsId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setOrderId($orderId);

    /**
     * Get order_increment_id
     * @return string|null
     */
    public function getOrderIncrementId();

    /**
     * Set order_increment_id
     * @param string $orderIncrementId
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setOrderIncrementId($orderIncrementId);

    /**
     * Get is_approved
     * @return string|null
     */
    public function getIsApproved();

    /**
     * Set is_approved
     * @param string $isApproved
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setIsApproved($isApproved);

    /**
     * Get coins
     * @return string|null
     */
    public function getCoins();

    /**
     * Set coins
     * @param string $coins
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setCoins($coins);

    /**
     * Get approved_by
     * @return string|null
     */
    public function getApprovedBy();

    /**
     * Set approved_by
     * @param string $approvedBy
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setApprovedBy($approvedBy);
	
	
    /**
     * Get approved_by
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set approved_by
     * @param string $name
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setCustomerName($name);
	
	
    /**
     * Get approved_by
     * @return string|null
     */
    public function getCustomerMobile();

    /**
     * Set approved_by
     * @param string $mobilenumber
     * @return \Mtwo\Coins\Coins\Api\Data\CoinsInterface
     */
    public function setCustomerMobile($mobilenumber);
}

