<?php

namespace Mtwo\Approver\Model\ResourceModel\FacultyApproval;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Approver\Model\FacultyApproval', 'Mtwo\Approver\Model\ResourceModel\FacultyApproval');
     }

}
?>