<?php
namespace Mtwo\RefrensInvoice\Model;

class Invoice extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\RefrensInvoice\Model\ResourceModel\Invoice');
    }
}
?>