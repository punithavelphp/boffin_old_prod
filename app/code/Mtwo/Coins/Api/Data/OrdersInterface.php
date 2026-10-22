<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api\Data;

interface OrdersInterface
{

    const CURRENT_STATUS = 'current_status';
    const SHIPPING_ADDRESS = 'shipping_address';
    const ORDERS_ID = 'orders_id';
    const IS_APPROVED = 'is_approved';
    const QUANTITY = 'quantity';
    const CATALOG_ID = 'catalog_id';
    const CUSTOMER_NAME = 'customer_name';
    const BILLING_ADDRESS = 'billing_address';
    const CUSTOMER_EMAIL = 'customer_email';

    /**
     * Get orders_id
     * @return string|null
     */
    public function getOrdersId();

    /**
     * Set orders_id
     * @param string $ordersId
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setOrdersId($ordersId);

    /**
     * Get customer_name
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer_name
     * @param string $customerName
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setCustomerName($customerName);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get catalog_id
     * @return string|null
     */
    public function getCatalogId();

    /**
     * Set catalog_id
     * @param string $catalogId
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setCatalogId($catalogId);

    /**
     * Get quantity
     * @return string|null
     */
    public function getQuantity();

    /**
     * Set quantity
     * @param string $quantity
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setQuantity($quantity);

    /**
     * Get is_approved
     * @return string|null
     */
    public function getIsApproved();

    /**
     * Set is_approved
     * @param string $isApproved
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setIsApproved($isApproved);

    /**
     * Get current_status
     * @return string|null
     */
    public function getCurrentStatus();

    /**
     * Set current_status
     * @param string $currentStatus
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setCurrentStatus($currentStatus);

    /**
     * Get shipping_address
     * @return string|null
     */
    public function getShippingAddress();

    /**
     * Set shipping_address
     * @param string $shippingAddress
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Get billing_address
     * @return string|null
     */
    public function getBillingAddress();

    /**
     * Set billing_address
     * @param string $billingAddress
     * @return \Mtwo\Coins\Orders\Api\Data\OrdersInterface
     */
    public function setBillingAddress($billingAddress);
}

