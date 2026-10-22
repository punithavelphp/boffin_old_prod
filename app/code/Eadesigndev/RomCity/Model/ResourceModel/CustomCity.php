<?php
namespace Eadesigndev\RomCity\Model\ResourceModel;

class CustomCity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('eadesign_romcity', 'entity_id');
    }
}
?>