<?php
namespace Mtwo\Brandimage\Model\ResourceModel;

class Brandimage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('brandimage', 'id');
    }
}
?>