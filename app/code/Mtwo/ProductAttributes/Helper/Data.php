<?php

namespace Mtwo\ProductAttributes\Helper;
 use Magento\Framework\App\ResourceConnection;

/**
 * Webkul Marketplace Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	public $resourceConnection;

     
    public function __construct(
       \Magento\Framework\App\Helper\Context $context
		,ResourceConnection $resourceConnection
    ) {
  		$this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    
    }
	public function deleteAllCityAttribute(){
		$attributeId = 224;
		$storeId = 0;
		$connection = $this->resourceConnection->getConnection();
 		$sellersSql = "select seller_id from marketplace_userdata where seller_id > 0 group by seller_id order by seller_id desc";
 		$sellers = $connection->fetchAll($sellersSql);
		
		$connection->query("DELETE FROM `catalog_product_entity_text` WHERE  `attribute_id` = '".$attributeId."'");
		
		foreach($sellers as $seller) {
			$sellerId = $seller['seller_id'];
			$query = "INSERT INTO `catalog_product_entity_text`( `attribute_id`, `store_id`, `entity_id`, `value`)  
			SELECT ".$attributeId.",0,a.mageproduct_id,GROUP_CONCAT(b.city_id order by b.city_id asc) as val FROM `marketplace_product`  as a
	left join seller_city as b on a.seller_id = b.seller_id
	where b.city_id is not null and b.seller_id = ".$sellerId." group by a.mageproduct_id";
 		$result = $connection->query($query);
		}
	}
	
	public function deleteCityAttributeBySeller($sellerId){
		$attributeId = 224;
		$storeId = 0;
		$connection = $this->resourceConnection->getConnection();
 		$connection->query("DELETE FROM `catalog_product_entity_text` WHERE  attribute_id = '".$attributeId."' `entity_id`  in (select mageproduct_id from marketplace_product where seller_id > 0 and seller_id = '".$sellerId."')");
 		$query = "INSERT INTO `catalog_product_entity_text`( `attribute_id`, `store_id`, `entity_id`, `value`)  
		SELECT ".$attributeId.",0,a.mageproduct_id,GROUP_CONCAT(b.city_id order by b.city_id asc) as val FROM `marketplace_product`  as a
left join seller_city as b on a.seller_id = b.seller_id
where b.city_id is not null and b.seller_id = ".$sellerId." group by a.mageproduct_id";
 		$result = $connection->query($query);
	}
 
}
