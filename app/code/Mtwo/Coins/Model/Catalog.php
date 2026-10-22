<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model;

use Magento\Framework\Model\AbstractModel;
use Mtwo\Coins\Api\Data\CatalogInterface;

class Catalog extends AbstractModel implements CatalogInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Mtwo\Coins\Model\ResourceModel\Catalog::class);
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
    public function getStock()
    {
        return $this->getData(self::STOCK);
    }

    /**
     * @inheritDoc
     */
    public function setStock($stock)
    {
        return $this->setData(self::STOCK, $stock);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }
}

