<?php

namespace Mtwo\Quote\Model\ResourceModel\Quote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Quote\Model\Quote', 'Mtwo\Quote\Model\ResourceModel\Quote');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>