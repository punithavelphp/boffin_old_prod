<?php
namespace Mtwo\Productupload\Model\ResourceModel;

class Productupload extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('csvdata', 'id');
    }
}
?>