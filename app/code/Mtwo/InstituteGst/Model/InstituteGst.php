<?php
namespace Mtwo\InstituteGst\Model;

use Magento\Framework\Model\AbstractModel;

class InstituteGst extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mtwo\InstituteGst\Model\ResourceModel\InstituteGst::class);
    }
}
