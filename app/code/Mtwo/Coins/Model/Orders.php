<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model;

use Magento\Framework\Model\AbstractModel;
use Mtwo\Coins\Api\Data\OrdersInterface;

class Orders extends AbstractModel implements OrdersInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Mtwo\Coins\Model\ResourceModel\Orders::class);
    }

    /**
     * @inheritDoc
     */
    public function getOrdersId()
    {
        return $this->getData(self::ORDERS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrdersId($ordersId)
    {
        return $this->setData(self::ORDERS_ID, $ordersId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getCatalogId()
    {
        return $this->getData(self::CATALOG_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCatalogId($catalogId)
    {
        return $this->setData(self::CATALOG_ID, $catalogId);
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->getData(self::QUANTITY);
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        return $this->setData(self::QUANTITY, $quantity);
    }

    /**
     * @inheritDoc
     */
    public function getIsApproved()
    {
        return $this->getData(self::IS_APPROVED);
    }

    /**
     * @inheritDoc
     */
    public function setIsApproved($isApproved)
    {
        return $this->setData(self::IS_APPROVED, $isApproved);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentStatus()
    {
        return $this->getData(self::CURRENT_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentStatus($currentStatus)
    {
        return $this->setData(self::CURRENT_STATUS, $currentStatus);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAddress($shippingAddress)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setBillingAddress($billingAddress)
    {
        return $this->setData(self::BILLING_ADDRESS, $billingAddress);
    }
}

