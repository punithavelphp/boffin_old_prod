<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Api\Data;

interface CatalogInterface
{

    const COINS = 'coins';
    const NAME = 'name';
    const STOCK = 'stock';
    const CATALOG_ID = 'catalog_id';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';
    const STATUS = 'status';

    /**
     * Get catalog_id
     * @return string|null
     */
    public function getCatalogId();

    /**
     * Set catalog_id
     * @param string $catalogId
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setCatalogId($catalogId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setName($name);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setDescription($description);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setStatus($status);

    /**
     * Get coins
     * @return string|null
     */
    public function getCoins();

    /**
     * Set coins
     * @param string $coins
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setCoins($coins);

    /**
     * Get stock
     * @return string|null
     */
    public function getStock();

    /**
     * Set stock
     * @param string $stock
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setStock($stock);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Mtwo\Coins\Catalog\Api\Data\CatalogInterface
     */
    public function setImage($image);
}

