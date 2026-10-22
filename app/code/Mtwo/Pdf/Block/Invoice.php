<?php

namespace Mtwo\Pdf\Block;

class Invoice extends \Magento\Framework\View\Element\Template
{

	public function _formatAddress($address)
	{
		$return = [];
		foreach (explode('|', $address) as $str) {
			foreach (str_split($str, 45) as $part) {
				if (empty($part)) {
					continue;
				}
				$return[] = $part;
			}
		}
		 
		return $return;
	}
	
	public function _formatCustomerAddress($customerAddres)
	{
		$customerAddressD = [];
		if(!empty($customerAddres['company'])){
			$customerAddressD[] = trim($customerAddres['company']);
		}else{
			$customerAddressD[] = trim($customerAddres['firstname'].' '.$customerAddres['lastname']);
		}
		
 				$customerAddressD[] = trim($customerAddres['street']);
				$customerAddressD[]  = $customerAddres['city'];
				$customerAddressD[]  = $customerAddres['region'];
 				//$customerAddressD[]  = $customerAddres['country_id'];
				$customerAddressD[]  = $customerAddres['postcode']; 
		return $customerAddressD;
	}

	public function getSellerDetails($sellerId){

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 

		$helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
		// $sellerId = $helper->getCustomerId();
		$collection = $helper->getSellerCollectionObj($sellerId);
		foreach ($collection as $row) {
			return $row;
		}
	}

	public function insertLogo($sellerId)
	{
		$store = null;
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 

		$helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
		$sellerId = $sellerId;

		$sellerImage = '';
		$sellerImageFlag = 0;
		// get seller data for store in which order is placed
		$collection = $helper->getSellerCollectionObj($sellerId);
		foreach ($collection as $row) {
			$sellerImage = $row->getLogoPic();
			if ($sellerImage) {
				$sellerImageFlag = 1;
			}
		}

		if ($sellerImage == '') {
			$sellerImage = $this->_scopeConfig
				->getValue(
				'sales/identity/logo',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
				$store
			);
			$sellerImageFlag = 0;
		}
		if ($sellerImage) {
			if ($sellerImageFlag == 0) {
				$sellerImagePath = 'sales/store/logo/'.$sellerImage;
			} else {
				$sellerImagePath = 'avatar/'.$sellerImage;
			}

			$fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
			$_mediaDirectory = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
			// ->getAbsolutePath();
			$URL_TYPE_MEDIA = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
				->getStore()
				->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			if ($_mediaDirectory->isFile($sellerImagePath)) {
				$sellerImage = \Zend_Pdf_Image::imageWithPath(
					$_mediaDirectory->getAbsolutePath($sellerImagePath)
				);
				$sellerImage =  $URL_TYPE_MEDIA.''.$sellerImagePath;
			
				return $sellerImage;
			} 
		}
		return false;	
	}

	public function insertAddress($sellerId)
	{ 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 

		$helper = $objectManager->get('Webkul\Marketplace\Helper\Data');

		/*$address = '';
		// get seller data for store in which order is placed
		$collection = $helper->getSellerCollectionObj($sellerId);
		foreach ($collection as $row) {
			$address = $row->getOthersInfo();
		}
		 
		
		if ($address == '') {
			$address = $this->_scopeConfig->getValue(
				'sales/identity/address',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE 
			);
		}
		$add = '';
		foreach (explode("\n", $address) as $value) {
			if ($value !== '') {
				$value = preg_replace('/<br[^>]*>/i', "\n", $value);
				foreach (str_split($value, 45) as $key=>$_value) {
					if(trim($_value) == '') continue;
					if($key == 0){
						$add .= '<br>';	
					}
					$add .= $_value;
				}
			}
		} */
		
		$add = '';
		$collection = $helper->getSellerCollectionObj($sellerId);
		foreach ($collection as $row) {
			
 			$regionNames = $objectManager->create('PHPCuong\Region\Model\RegionName')
				->getCollection()
				->addFieldToFilter('region_id',$row->getData('region_id'));
			
			$add .= trim($row->getData('address_1')).'<br>';
			$add .= trim($row->getData('address_2')).'<br>';
			$add .= $row->getData('city').'<br>';
			foreach($regionNames as $regionName) {
				$add .= $regionName->getName().'<br>';
			}
			$add .= $row->getData('postalcode').'<br>';
		}
		
		return $add;
	}
	public function insertStoreAddress() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
				$_storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

		$add=	$_storeManager->getStore()->getName();
		$add .= '<br>';
		$add .=	"46, VelapparNagar,Madhiraveedu, ";
		$add .= '<br>';
		$add .=	"Thiruverkadu, Chennai - 600-077";
 		return $add;
	}
	
	public function getIndianCurrency(float $number)
		{
			$decimal = round($number - ($no = floor($number)), 2) * 100;
			$hundred = null;
			$digits_length = strlen($no);
			$i = 0;
			$str = array();
			$words = array(0 => '', 1 => 'one', 2 => 'two',
				3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
				7 => 'seven', 8 => 'eight', 9 => 'nine',
				10 => 'ten', 11 => 'eleven', 12 => 'twelve',
				13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
				16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
				19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
				40 => 'forty', 50 => 'fifty', 60 => 'sixty',
				70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
			$digits = array('', 'hundred','thousand','lakh', 'crore');
			while( $i < $digits_length ) {
				$divider = ($i == 2) ? 10 : 100;
				$number = floor($no % $divider);
				$no = floor($no / $divider);
				$i += $divider == 10 ? 1 : 2;
				if ($number) {
					$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
					$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
					$str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
				} else $str[] = null;
			}
			$Rupees = implode('', array_reverse($str));
		 
			$paise = ($decimal > 0) ? " and " . ($words[(int)($decimal / 10)] . " " . $words[(((int)$decimal) % 10)]) . ' Paise ' : '';
			return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
		}


	public function insertStoreLogo() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
  
					$logoM = $objectManager->get('Magento\Theme\Block\Html\Header\Logo');
return "https://boffinbutler.com/media/logo/websites/1/Boffin-Brand-LOGO.png";
		 return $logoM->getLogoSrc();
 		
	}
		public function getSeller($sellerId){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 			$seller = $objectManager->get('Webkul\Marketplace\Model\Seller');
			$seller->load($sellerId,"seller_id");
			return $seller;
	}
	public function getProduct($productId){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 			$product = $objectManager->get('Magento\Catalog\Model\Product');
			$product->load($productId);
			return $product;
	}
	public function getQuoteDetails($quoteId){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 			$quote = $objectManager->get('Mtwo\Quote\Model\Quote');
			$quote->load($quoteId);
			return $quote;
	}
	public function getQuoteDetailItems($quoteId){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 			$quoteItems = $objectManager->get('Mtwo\Quote\Model\QuoteItems')->getCollection();
			$quoteItems->addFieldToFilter('quote_id',$quoteId);
			return $quoteItems;
	}
	
}
