<?php
namespace Mtwo\Approver\Model;

class EmployeeApprover extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Approver\Model\ResourceModel\EmployeeApprover');
    }
}
?>