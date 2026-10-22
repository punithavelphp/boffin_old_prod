<?php

namespace Mtwo\Quote\Model\ResourceModel\QuoteItems;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Quote\Model\QuoteItems', 'Mtwo\Quote\Model\ResourceModel\QuoteItems');
     }

}
?>