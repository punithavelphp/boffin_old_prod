<?php

namespace Mtwo\RefrensInvoice\Model\ResourceModel\Invoice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\RefrensInvoice\Model\Invoice', 'Mtwo\RefrensInvoice\Model\ResourceModel\Invoice');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
	
	    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['sales_invoice' => $this->getTable('sales_invoice')],
            'main_table.invoice_id = sales_invoice.entity_id',
            ['base_grand_total','created_at','magento_increment_id'=>'sales_invoice.increment_id']
        );
    }


}
?>