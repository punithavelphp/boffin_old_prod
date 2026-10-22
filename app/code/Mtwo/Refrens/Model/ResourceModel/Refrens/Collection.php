<?php

namespace Mtwo\Refrens\Model\ResourceModel\Refrens;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\Refrens\Model\Refrens', 'Mtwo\Refrens\Model\ResourceModel\Refrens');
     }

}
?>