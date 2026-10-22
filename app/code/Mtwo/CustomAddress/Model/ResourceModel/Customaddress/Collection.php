<?php

namespace Mtwo\CustomAddress\Model\ResourceModel\Customaddress;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\CustomAddress\Model\Customaddress', 'Mtwo\CustomAddress\Model\ResourceModel\Customaddress');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>