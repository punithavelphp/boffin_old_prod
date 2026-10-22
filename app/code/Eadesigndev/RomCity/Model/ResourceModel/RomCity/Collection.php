<?php


namespace Eadesigndev\RomCity\Model\ResourceModel\RomCity;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class  Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            'Emc\RomCity\Model\RomCity',
            'Emc\RomCity\Model\ResourceModel\RomCity'
        );
    }
}
