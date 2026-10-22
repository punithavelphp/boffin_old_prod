<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Block\Product;
 
class Upload extends \Magento\Framework\View\Element\Template
{
    
	public function getRemainingCount(){
		
	 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$tableName = $resource->getTableName('csvdata'); //gives table name with prefix

			$customersession = $objectManager->get('Magento\Customer\Model\Session');
			$customer_id = $customersession->getCustomerId();
 
			$sql = "Select count(*) as remaining FROM " . $tableName." where status = 0 and customer_id=".$customer_id;
			$result = $connection->fetchAll($sql);  
  			return $result[0]['remaining'];
	}
	
	
}
