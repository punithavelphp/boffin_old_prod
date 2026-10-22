<?php
namespace Mtwo\Quote\Model;

class Quote extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Quote\Model\ResourceModel\Quote');
    }
}
?>