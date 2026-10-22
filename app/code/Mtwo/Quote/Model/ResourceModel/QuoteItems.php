<?php
namespace Mtwo\Quote\Model\ResourceModel;

class QuoteItems extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('custom_quote_items', 'id');
    }
}
?>