<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Coins extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('mtwo_coins_coins', 'coins_id');
    }
}

