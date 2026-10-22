<?php

namespace Emc\Otp\Model;

use Magento\Framework\Model\AbstractModel;

class SmsLog extends AbstractModel
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('Emc\Otp\Model\ResourceModel\SmsLog');
    }
}
