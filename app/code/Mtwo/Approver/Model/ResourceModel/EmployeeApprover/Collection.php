<?php

namespace Mtwo\Approver\Model\ResourceModel\EmployeeApprover;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Approver\Model\EmployeeApprover', 'Mtwo\Approver\Model\ResourceModel\EmployeeApprover');
       // $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>