<?php
	
	namespace Mtwo\Productupload\Model;
	
	use Mtwo\Productupload\Api\HsnupdateInterface;
	
	class Hsnupdate  {
		
		protected $_productRepository;
		protected $logger;
		protected $_productFactory;
	 
		
		public function __construct(
		\Magento\Catalog\Api\ProductRepositoryInterface $_productRepository
 		, \Psr\Log\LoggerInterface $logger
 		, \Magento\Catalog\Model\ProductFactory $_productFactory
 		) {
			$this->_productRepository = $_productRepository;
 			$this->_productFactory = $_productFactory;
 		}
		
		public function updateHsncode($productId,$hsncode) {
		 	 $product = $this->_productFactory->create()->load($productId);
		 	 $product->setData('hsncode',$hsncode)->save();
			return array('reponse'=>array('status'=>true));
		} 
}						