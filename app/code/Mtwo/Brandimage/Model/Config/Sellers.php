<?php

namespace Mtwo\Brandimage\Model\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class Sellers extends AbstractSource {
	protected $optionFactory;
	public function getAllOptions()
	{
		$this->_options = [];
 		$objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
		$connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection(); 
		$sql = "select * from marketplace_userdata where is_seller = 1 and store_id=0";
		$sellerData =  $connection->fetchAll($sql);
		foreach($sellerData as $sellerD){
			$this->_options[] = ['label' => $sellerD['shop_title'],'value'=> $sellerD['seller_id']];
		}
		return $this->_options;
	}
}