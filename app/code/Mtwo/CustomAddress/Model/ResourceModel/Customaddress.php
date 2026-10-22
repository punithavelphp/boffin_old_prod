<?php
namespace Mtwo\CustomAddress\Model\ResourceModel;

class Customaddress extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_address_entity', 'entity_id');
    }
}
?>