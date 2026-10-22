<?php
namespace Mtwo\Approver\Model\ResourceModel;

class FacultyApproval extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('faculty_approval', 'id');
    }
}
?>