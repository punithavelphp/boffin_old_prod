<?php
namespace Mtwo\Approver\Model\ResourceModel;

class Approvers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('approvers', 'id');
    }
}
?>