<?php

namespace Mtwo\OtpGrid\Model\ResourceModel\Otp;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\OtpGrid\Model\Otp', 'Mtwo\OtpGrid\Model\ResourceModel\Otp');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>