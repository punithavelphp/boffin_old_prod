<?php
namespace Mtwo\Productupload\Model;

class Productupload extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Productupload\Model\ResourceModel\Productupload');
    }
}
?>