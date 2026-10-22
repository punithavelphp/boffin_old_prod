<?php


namespace Emc\Otp\Model\ResourceModel\SmsLog;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class  Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            'Emc\Otp\Model\SmsLog',
            'Emc\Otp\Model\ResourceModel\SmsLog'
        );
    }
}
