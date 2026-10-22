<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Model\ResourceModel\Coins;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'coins_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Mtwo\Coins\Model\Coins::class,
            \Mtwo\Coins\Model\ResourceModel\Coins::class
        );
    }
	
	protected function _initSelect()
	{
		parent::_initSelect();

		$this->getSelect()->joinLeft(
				['cus' => $this->getTable('customer_grid_flat')],
				'main_table.customer_id = cus.entity_id',
				['customer_name'=>'cus.name','customer_mobile'=>'cus.mobilenumber']
			);
		
		
	 $this->addFilterToMap('customer_name', 'customer_name');
	 $this->addFilterToMap('customer_mobile', 'customer_mobile');
		//echo $this->getSelect();
		return $this;
	}

}

