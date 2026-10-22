<?php
namespace Mtwo\Refrens\Model\ResourceModel;

class Refrens extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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