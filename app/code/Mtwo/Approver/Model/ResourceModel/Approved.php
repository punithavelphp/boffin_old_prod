<?php
namespace Mtwo\Approver\Model\ResourceModel;

class Approved extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('approved_by', 'id');
    }
}
?>