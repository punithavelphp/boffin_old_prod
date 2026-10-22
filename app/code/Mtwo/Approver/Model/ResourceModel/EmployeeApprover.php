<?php
namespace Mtwo\Approver\Model\ResourceModel;

class EmployeeApprover extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('employee_order_approver_list', 'id');
    }
}
?>