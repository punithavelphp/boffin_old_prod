<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Productslider
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mtwo\Coins\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\Productslider\Model\Config\Source\Location;

/**
 * Class CommentContent
 * @package Mageplaza\Blog\Ui\Component\Listing\Columns
 */
class TotalCoins extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
	protected $customerArray = [];
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['coins'])) {
                    $data = $this->getCoins($item['customer_id']);
  
                    $item['total_coins'] =  $data;
					 $customerData = $this->getCustomer($item['customer_id']);
                    $item['customer_name'] =  $customerData['name'];
                    $item['customer_mobile'] =  $customerData['mobilenumber'];

					
                 }
				 

            }
			
        }

        return $dataSource;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function getCoins($customer_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$connection = $objectManager->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
		$totalCoinsSelect = $connection->select()
			->from('mtwo_coins_coins', [new \Zend_Db_Expr('SUM(coins)')])
			->where('customer_id ='.$customer_id)
			->group('customer_id')
			->limit(1);

		$totalCoins = $connection->fetchOne($totalCoinsSelect);

        return $totalCoins;
    }
	
	public function getCustomer($customer_id)
    {
		
		if(isset($this->customerArray[$customer_id])){
			return $this->customerArray[$customer_id];	
		}
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$connection = $objectManager->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
		$totalCoinsSelect = $connection->select()
			->from('customer_grid_flat', array('name','mobilenumber'))
			->where('entity_id ='.$customer_id)
 			->limit(1);

		$customerData = $connection->fetchAll($totalCoinsSelect);
		if(isset($customerData) && isset($customerData[0])) {
			$customer = $customerData[0];
			$this->customerArray[$customer_id] = array('name'=>$customer['name'],'mobilenumber'=>$customer['mobilenumber']);
			return 	$this->customerArray[$customer_id];
		}
			return array('name'=>'a','mobilenumber'=>'s');	
    }
}
