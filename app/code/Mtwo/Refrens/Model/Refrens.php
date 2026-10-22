<?php
namespace Mtwo\Refrens\Model;

class Refrens extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Refrens\Model\ResourceModel\Refrens');
    }
}
?>