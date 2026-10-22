<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model;

use Magento\Framework\Model\AbstractModel;
use Mtwo\Coins\Api\Data\CoinsInterface;

class Coins extends AbstractModel implements CoinsInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Mtwo\Coins\Model\ResourceModel\Coins::class);
    }

    /**
     * @inheritDoc
     */
    public function getCoinsId()
    {
        return $this->getData(self::COINS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCoinsId($coinsId)
    {
        return $this->setData(self::COINS_ID, $coinsId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderIncrementId($orderIncrementId)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
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
    public function getCoins()
    {
        return $this->getData(self::COINS);
    }

    /**
     * @inheritDoc
     */
    public function setCoins($coins)
    {
        return $this->setData(self::COINS, $coins);
    }

    /**
     * @inheritDoc
     */
    public function getApprovedBy()
    {
        return $this->getData(self::APPROVED_BY);
    }

    /**
     * @inheritDoc
     */
    public function setApprovedBy($approvedBy)
    {
        return $this->setData(self::APPROVED_BY, $approvedBy);
    }
	/**
     * @inheritDoc
     */
    public function getCustomerName()
    {
        return $this->getData('customer_name');
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName($customer_name)
    {
        return $this->setData('customer_name', $customer_name);
    }
	 public function getCustomerMobile()
    {
        return $this->getData('customer_mobile');
    }

    /**
     * @inheritDoc
     */
    public function setCustomerMobile($customer_mobile)
    {
        return $this->setData('customer_mobile', $customer_mobile);
    }
}

