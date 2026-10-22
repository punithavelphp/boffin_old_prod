<?php

namespace Mtwo\Approver\Model\ResourceModel\Approvers;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Approver\Model\Approvers', 'Mtwo\Approver\Model\ResourceModel\Approvers');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>