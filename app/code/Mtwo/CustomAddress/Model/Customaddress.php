<?php
namespace Mtwo\CustomAddress\Model;

class Customaddress extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\CustomAddress\Model\ResourceModel\Customaddress');
    }
}
?>