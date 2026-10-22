<?php
namespace Mtwo\Approver\Model\ResourceModel;

class SellerCity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('seller_city', 'id');
    }
}
?>