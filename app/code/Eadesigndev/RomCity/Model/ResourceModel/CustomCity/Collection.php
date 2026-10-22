<?php

namespace Eadesigndev\RomCity\Model\ResourceModel\CustomCity;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Eadesigndev\RomCity\Model\CustomCity', 'Eadesigndev\RomCity\Model\ResourceModel\CustomCity');
     }

}
?>