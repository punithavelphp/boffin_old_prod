<?php
namespace Mtwo\RefrensInvoice\Model\ResourceModel;

class Invoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_invoice_refrens', 'id');
    }
}
?>