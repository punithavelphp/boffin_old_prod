<?php 
namespace Eadesigndev\RomCity\Model;

use Magento\Framework\Model\AbstractModel;

class City extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Eadesigndev\RomCity\Model\ResourceModel\RomCity');
    }
}