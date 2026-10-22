<?php
namespace Mtwo\Brandimage\Model;

class Brandimage extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Brandimage\Model\ResourceModel\Brandimage');
    }
}
?>