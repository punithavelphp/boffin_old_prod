<?php

namespace Emc\Otp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class SmsLog extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('sms_log', 'id');
    }
}
