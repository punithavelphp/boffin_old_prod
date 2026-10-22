<?php
namespace Eadesigndev\RomCity\Model;

class CustomCity extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Eadesigndev\RomCity\Model\ResourceModel\CustomCity');
    }
}
?>