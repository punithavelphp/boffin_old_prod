<?php
namespace Mtwo\InstituteGst\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InstituteGst extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('gst_institute', 'id');
    }
}
