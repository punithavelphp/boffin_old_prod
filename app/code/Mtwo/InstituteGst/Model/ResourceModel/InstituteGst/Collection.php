<?php
namespace Mtwo\InstituteGst\Model\ResourceModel\InstituteGst;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Mtwo\InstituteGst\Model\InstituteGst::class,
            \Mtwo\InstituteGst\Model\ResourceModel\InstituteGst::class
        );
    }
}
