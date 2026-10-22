<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model\ResourceModel\Orders;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'orders_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Mtwo\Coins\Model\Orders::class,
            \Mtwo\Coins\Model\ResourceModel\Orders::class
        );
    }
}

