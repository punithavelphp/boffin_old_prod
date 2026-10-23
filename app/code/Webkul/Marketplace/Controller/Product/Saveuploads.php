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

namespace Webkul\Marketplace\Controller\Product;
use Magento\Framework\App\Action\Context;
use Shuchkin\SimpleXLSX;
use Magento\Framework\App\Filesystem\DirectoryList;
use Webkul\Marketplace\Helper\Data as MarketplaceHelperData;
use Webkul\Marketplace\Model\Product as SellerProduct;
use Magento\Downloadable\Api\Data\SampleInterfaceFactory as SampleFactory;
use Magento\Downloadable\Api\Data\LinkInterfaceFactory as LinkFactory;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as MpProductCollection;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Catalog\Model\CategoryFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Filter\FilterInput;
/**
		* Webkul Marketplace SaveProduct controller.
	*/
class Saveuploads extends \Magento\Customer\Controller\AbstractAccount
{
	public $_linkResolver;
	public $_mpProductFactory;
	public $_mpProductCollectionFactory;
	public $_stockRegistryProvider;
	public $_stockConfiguration;
	public $_jsonHelper;
	public $_fileUploaderFactory;
	public $_directoryList;
	public $_customerSession;
	public $_storeManager;
	public $_ioAdapter;
	public $_csvProcessor;
	public $_productResourceModel;
	public $_resources;
	public $_connection;

	/**
			* @var \Magento\Framework\Stdlib\DateTime\DateTime
		*/
	protected $_date;

	/**
			* @var \Magento\Framework\Filesystem\Directory\WriteInterface
		*/
	protected $_mediaDirectory;

	/**
			* @var Builder
		*/
	protected $_marketplaceProductBuilder;

	/**
			* @var \Magento\Catalog\Model\Product\TypeTransitionManager
		*/
	protected $_catalogProductTypeManager;

	/**
			* @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
		*/
	protected $_catalogProductTypeConfigurable;

	/**
			* @var \Magento\ConfigurableProduct\Model\Product\VariationHandler
		*/
	protected $_variationHandler;

	/** @var \Magento\Catalog\Api\ProductRepositoryInterface  */
	protected $_productRepositoryInterface;

	/** @var \Magento\Catalog\Api\ProductLinkInterfaceFactory  */
	protected $_productLinkFactory;

	/**
			* @var eventManager
		*/
	protected $_eventManager;

	/**
			* @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks
		*/
	protected $_productLinks;

	/**
			* @var \Magento\Backend\Helper\Js
		*/
	protected $_jsHelper;

	/**
			* @var \Magento\Framework\Stdlib\DateTime\Filter\Date
		*/
	protected $_dateFilter;

	/**
			* @var MarketplaceHelperData
		*/
	protected $_marketplaceHelperData;

	/**
			* @var SampleFactory
		*/
	protected $sampleFactory;

	/**
			* @var LinkFactory
		*/
	protected $linkFactory;

	/**
			* @var \Magento\Downloadable\Model\Sample\Builder
		*/
	protected $sampleBuilder;

	/**
			* @var \Magento\Downloadable\Model\Link\Builder
		*/
	protected $linkBuilder;

	/**
			* @var \Magento\Catalog\Model\Product\Link\Resolver
		*/
	protected $linkResolver;

	/**
			* @var \Magento\Framework\Registry
		*/
	protected $_registry;

	/**
			* @var CategoryLinkManagementInterface
		*/
	protected $categoryLinkManagement;

	/**
			* @var \Magento\Catalog\Model\Product\Copier
		*/
	protected $productCopier;

	/**
			* @var \Magento\Framework\Message\ManagerInterface
		*/
	protected $_messageManager;

	/**
			* @var CustomerFactory
		*/
	protected $customerModel;

	/**
			* @var CategoryFactory
		*/
	protected $categoryModel;

	/**
			* @var MpEmailHelper
		*/
	protected $mpEmailHelper;

	/**
			* @var ProductCustomOptionInterfaceFactory
		*/
	protected $productCustomOption;


	protected $_redirectFactory;

	protected $catalogProductSave;

	/**
			* @param \Magento\Framework\Event\Manager                                  $eventManager
			* @param \Magento\Framework\Stdlib\DateTime\DateTime                       $date
			* @param \Magento\Framework\Stdlib\DateTime\Filter\Date                    $dateFilter,
			* @param \Magento\Catalog\Model\Product\TypeTransitionManager              $catalogProductTypeManager
			* @param \Magento\ConfigurableProduct\Model\Product\VariationHandler       $variationHandler
			* @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable      $productTypeConfigurable
			* @param \Magento\Catalog\Api\ProductRepositoryInterface                   $productRepositoryInterface
			* @param \Magento\Catalog\Api\ProductLinkInterfaceFactory                  $productLinkFactory
			* @param \Magento\Catalog\Model\Product\Link\Resolver                      $linkResolver
			* @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $productLinks
			* @param Filesystem                                                        $filesystem
			* @param Builder                                                           $catalogProductBuilder
			* @param MarketplaceHelperData                                             $marketplaceHelperData
			* @param \Magento\Framework\Registry                                       $registry
			* @param \Magento\Catalog\Model\Product\Copier                             $productCopier
			* @param \Magento\Framework\Message\ManagerInterface                       $messageManager
			* @param CustomerFactory                                                   $customerModel
			* @param CategoryFactory                                                   $categoryModel
			* @param MpEmailHelper                                                     $mpEmailHelper
			* @param ProductCustomOptionInterfaceFactory                               $productCustomOption
			* @SuppressWarnings(PHPMD.ExcessiveParameterList)
		*/
	public function __construct(       
		Context $context,
		\Magento\Framework\Event\Manager $eventManager,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
		\Magento\Catalog\Model\Product\TypeTransitionManager $catalogProductTypeManager,
		\Magento\ConfigurableProduct\Model\Product\VariationHandler $variationHandler,
		\Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeConfigurable,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
		\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkFactory,
		\Magento\Catalog\Model\Product\Link\Resolver $linkResolver,
		\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $productLinks,
		\Magento\Backend\Helper\Js $jsHelper,
		\Magento\Framework\Filesystem $filesystem,
		Builder $marketplaceProductBuilder,
		MarketplaceHelperData $marketplaceHelperData,
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Model\Product\Copier $productCopier,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
		MpProductCollection $mpProductCollectionFactory,
		StockRegistryProviderInterface $stockRegistryProvider,
		StockConfigurationInterface $stockConfiguration,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		CustomerFactory $customerModel,
		CategoryFactory $categoryModel,
		MpEmailHelper $mpEmailHelper,
		ProductCustomOptionInterfaceFactory $productCustomOption,
		\Magento\Downloadable\Model\Link\Builder $linkBuilder,
		LinkFactory $linkFactory,
		CategoryLinkManagementInterface $categoryLinkManagement,
		\Magento\Downloadable\Model\Sample\Builder $sampleBuilder,
		SampleFactory $sampleFactory,
		DirectoryList $directoryList 
		, \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
		, \Magento\Framework\File\Csv $csvProcessor
		,Session $customerSession
		,\Magento\Store\Model\StoreManagerInterface $storeManager
		, RedirectFactory $redirectFactory    
		, \Magento\Framework\Filesystem\Io\File $ioAdapter
		, \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
		,\Magento\Framework\App\ResourceConnection $resourceConnection

	) {
		$this->_eventManager = $eventManager;
		$this->_date = $date;
		$this->_dateFilter = $dateFilter;
		$this->_catalogProductTypeManager = $catalogProductTypeManager;
		$this->_variationHandler = $variationHandler;
		$this->_catalogProductTypeConfigurable = $productTypeConfigurable;
		$this->_productRepositoryInterface = $productRepositoryInterface;
		$this->_productLinkFactory = $productLinkFactory;
		$this->_linkResolver = $linkResolver;
		$this->_productLinks = $productLinks;
		$this->_jsHelper = $jsHelper;
		$this->_marketplaceProductBuilder = $marketplaceProductBuilder;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$this->_marketplaceHelperData = $marketplaceHelperData;
		$this->_registry = $registry;
		$this->productCopier = $productCopier;
		$this->_messageManager = $messageManager;
		$this->_mpProductFactory = $mpProductFactory;
		$this->_mpProductCollectionFactory = $mpProductCollectionFactory;
		$this->_stockRegistryProvider = $stockRegistryProvider;
		$this->_stockConfiguration = $stockConfiguration;
		$this->_jsonHelper = $jsonHelper;
		$this->customerModel = $customerModel;
		$this->categoryModel = $categoryModel;
		$this->mpEmailHelper = $mpEmailHelper;
		$this->productCustomOption = $productCustomOption;
		$this->linkBuilder = $linkBuilder;
		$this->linkFactory = $linkFactory;
		$this->categoryLinkManagement = $categoryLinkManagement;
		$this->sampleBuilder = $sampleBuilder;
		$this->sampleFactory = $sampleFactory;
		$this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_directoryList = $directoryList;
		$this->_customerSession = $customerSession;
		$this->_storeManager = $storeManager;
		$this->_redirectFactory = $redirectFactory;
		$this->_ioAdapter = $ioAdapter;
		$this->_csvProcessor = $csvProcessor;
		$this->_productResourceModel = $productResourceModel;
		$this->_resources = $resourceConnection;
		$this->_connection	 = $this->_resources->getConnection();
		parent::__construct( $context );

	}

	/**
			* saveProductData method for seller's product save action.
			*
			* @param $sellerId
			* @param $wholdata
			*
			* @return array
		*/

	public function execute() {


		$importType = $this->getRequest()->getPost();
		$rem_URL = $this->_storeManager->getStore()->getBaseUrl();
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($this->getRequest()->isPost()) {
			$target = $this->getUploadPath();
			$uploader = $this->_fileUploaderFactory->create(['fileId' => 'massupload_csv']);
			$uploader->setAllowedExtensions(['csv','xls','xlsx']);
			$uploader->setAllowRenameFiles(true);
			$extension = $uploader->getFileExtension();
			$save_file_name = date('Y-m-d-H-i-s') . '.'.$extension;
			$result = $uploader->save($target, $save_file_name);
			$uploaded_file_name = $uploader->getUploadedFileName();
			$ret = $this->readCSVFile($uploaded_file_name,$extension);

		}

		$resultRedirect = $this->_redirectFactory->create();
		$resultRedirect->setUrl($this->_url->getUrl('marketplace/account/addproducts'));
		return $resultRedirect;
	}


	public function saveToDb($customer_id,$category_id,$rowSku,$rowData){
		$tableName 			= $this->_resources->getTableName('csvdata');
		//	$rowData = array_map("utf8_encode", $rowData); // added new line

		$rowDataIns = serialize($rowData);
		$is_error = 0;
		$insertData['data'] = $rowDataIns;
		$insertData['status'] = $is_error;
		$insertData['sku'] = $rowSku;
		$insertData['customer_id'] = $customer_id;
		$insertData['category_id'] = $category_id;
		$updateFields[] = 'status';
		$updateFields[] = 'data';
		$updateFields[] = 'category_id';

		try{
			$this->_connection->insertOnDuplicate($tableName,$insertData,$updateFields);  

		} catch (\Exception $e) {	
			echo $e->getMessage();die;
		}

		$sql = "INSERT INTO ".$tableName." (data, status, sku,customer_id,category_id) VALUES ('".$rowDataIns."', '".$is_error."', '".$rowSku."','".$customer_id."','".$category_id."') ON DUPLICATE KEY UPDATE `category_id`='".$category_id."', `data`    = '".$rowDataIns."', `status` = '".$is_error."'";
		//$this->_connection->query($sql);  
	}


	public function addGstToPrice($price,$gst){
		$price = $price + (($price * $gst)/100);
		return $price;
	}

	public function createProducts(){




		$customer_id = $this->_customerSession->getCustomerId();
		$tableName 			= $this->_resources->getTableName('csvdata');
		$sql = "select * from ".$tableName." where status = 0 and customer_id= '".$customer_id."' order by rand()  limit 50";
		$dataCSV = $this->_connection->fetchAll($sql);




		if(is_array($dataCSV) && count($dataCSV)){

		}else{

			$message['message'] = 'Completed';
			$message['status'] = false;

			return $message;	
		}

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$attrhelper = $objectManager->create('\Webkul\Marketplace\Helper\AttrHelper');

		foreach($dataCSV as $data){
			$data_array = unserialize($data['data']);


			$category_id = $data['category_id'];	
			$data_array['sellingprice'] = $this->priceclean($data_array['sellingprice']);

			if($data_array['sellingprice'] == '' || $data_array['sellingprice'] == $data_array['price']){
				$data_array['sellingprice'] = 	$data_array['price'];
			}else{ 
				$data_array['special_price'] = $data_array['sellingprice'];
				$data_array['special_from_date'] = date('m/d/Y');
				//  $data_array['special_to_date'] =  04/30/2022
			}
			$nreq_array = array('hsncode','image','productdescription','type','grade','targetorganism','molecularformula','purity','material');
			$req_array = array('productid','productname','availablestock','brand','price');
			$notexist = false;
			foreach($req_array as $val) {
				if(!isset($data_array[$val]) || strlen($data_array[$val]) < 0){
					$returnArr = [];
					$returnArr['error'] = 1;
					$returnArr['product_id'] = '';
					$returnArr['sku'] = '';
					$returnArr['message'] = strtoupper($val).' is reqired';
					$notexist = true;
					break;
				}
			}
			if($notexist){
				$error[] = $returnArr;
				// return $returnArr;
			}else{ 	
				/*$customer_id = $this->_customerSession->getCustomerId();
				$category_id = $this->getRequest()->getPost('category_id');
				$sku = strtolower($data_array['productid']);
				$this->saveToDb($customer_id,$category_id,$sku,$data_array);
				continue;
				*/
				$attrArr = $data_array;
				foreach($req_array as $val) {
					if('brand' == $val) {
						continue;
					}
					unset($attrArr[$val]);
				}
				if(isset($attrArr['deliverytime'])){
					unset($attrArr['deliverytime']);
				}				
				$pid = false;
				$wholedata = array();
				$data_array['productid'] = strtolower($data_array['productid']);
				$data_array['productid'] = $data_array['productid'].'_'.$this->_customerSession->getCustomerId();

				if(isset($data_array['productid'])) {

					try {
						$id = $this->_productResourceModel->getIdBySku($data_array['productid']);
						if ($id) {
							$pid = true;
							$wholedata['id'] = $id;
						}
					} catch (\Exception $e) {	

					}

				}  


				if(isset($wholedata['id']) && $wholedata['id'] != '' && isset($data_array['productdescription']) && $data_array['productdescription'] == '')	 {
					$data_array['productdescription'] = $data_array['productname'];
				} 
				//$data_array['productdescription'] = $data_array['productname'];

				$wholedata['set'] = 4;
				$wholedata['type'] = 'simple';

				if(!$pid){
					$url_key = $this->createUrlKey( $data_array['productname'], $data_array['productid']);
					$wholedata['product']['url_key'] = $url_key;
				} 


				if(isset($data_array['productdescription']) && $data_array['productdescription'] != '')	 {

				} else {
					$data_array['productdescription'] = $data_array['productname'];
				}

				$wholedata['product']['category_ids'][] = '2';
				$wholedata['product']['category_ids'][] = '43';
				$wholedata['product']['category_ids'][] =$category_id;
				$wholedata['product']['name'] = $data_array['productname'];
				//$wholedata['product']['brand'] = $data_array['brand'];
				$wholedata['product']['short_description'] = $data_array['productdescription'];
				$wholedata['product']['description'] = $data_array['productdescription'];
				$wholedata['product']['sku'] = $data_array['productid'];
				$price = $this->priceclean($data_array['price']);
				if(empty($price) || $price == ''){
					$price = 0;
				}
				$gst = 0;
				if(isset($data_array['gst']) && $data_array['gst'] > 0){
					$gst = $data_array['gst'];
				}



				$price = $this->addGstToPrice($price,$gst);

				if(isset($data_array['special_price'])){
					$selling_price = $this->addGstToPrice($data_array['special_price'],$gst);
					$data_array['special_price'] = 	$selling_price;
					$wholedata['product']['special_price'] = $selling_price;	
				}
				$wholedata['product']['price'] = $price;	

				$wholedata['product']['tax_class_id'] = 0;
				if(isset($data_array['tax_class_id'])){
					$wholedata['product']['tax_class_id'] = $data_array['tax_class_id'];
				}
				if(isset($data_array['casnumber'])) {
					$wholedata['product']['casnumber'] = $data_array['casnumber'];
				} 

				foreach($attrArr as $attributeCode=>$attributeValue){
					if($attributeCode == 'quantity'){
						$attributeCode = 'quantity_val';
					}
					if($attributeValue != '' && $attributeCode != 'casnumber' 
					   &&  $attributeCode != 'tax_class_id'
					   &&  $attributeCode != 'productdescription' && $attributeCode != 'image' && $attributeCode != 'sellingprice' && $attributeCode != 'special_price') {



						$attributeValue = trim($attributeValue);
						$wholedata['product'][$attributeCode] = $attrhelper->createOrGetId($attributeCode, $attributeValue);
					}
				}

				$image = '';

				if(isset($data_array['image']) && $data_array['image'] != ''){
					try {
						$url = $data_array['image'] ;
						$response = $this->fetchUrl($url);
						if($response){
							$path = $this->getImageUploadPath();
							$pathinfo = pathinfo($url); // To get the filename and extension
							\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(json_encode($pathinfo));




							if(isset($pathinfo['extension'])) {
								$ext = $pathinfo['extension'];
								$filename = $path.$pathinfo['filename'];
								$img = $response; // get the image from the url


								$ext = strtolower($ext);
								switch ($ext) {
									case "jpeg":
										$ext = 'jpeg';
										break;
									case "jpg":
										$ext = 'jpg';
										break;
									case "png":
										$ext = 'png';
										break;

									default:
										$ext = 'jpg';
								}



								file_put_contents($filename.'.'.$ext, $img);  




								$image = $filename.'.'.$ext;



							}
						}
					} catch (Exception $e) {
						$image = '';
					}

				}


				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($image);
				if(isset($data_array['deliverytime'])){
					$wholedata['product']['deliverytime'] = $data_array['deliverytime'];
				}

				$wholedata['product']['stock_data']['manage_stock'] = 1;
				$wholedata['product']['stock_data']['use_config_manage_stock'] = 1;

				$wholedata['product']['quantity_and_stock_status']['qty'] = $data_array['availablestock']; // $data_array['qty']
				$wholedata['product']['quantity_and_stock_status']['is_in_stock'] = 1;
				$wholedata['product']['visibility'] = 4;
				//$wholedata['product']['tax_class_id'] = 0;
				$wholedata['product']['product_has_weight'] = 1;
				$wholedata['product']['weight'] = 1;
				$wholedata['product']['meta_title'] =''; 
				$wholedata['product']['meta_keyword'] =''; 
				$wholedata['product']['meta_description'] ='';
				if($image != '' && isset($wholedata['id']) && $wholedata['id'] != ''){
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
					$allGalleries = $product->getMediaGalleryEntries();
					foreach ($product->getMediaGalleryEntries() as $key => $entry){
						$types = $entry->getData('types');
						if(!empty($types)) {
							unset($allGalleries[$key]);
						}
					}

					$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');

					$product->setMediaGalleryEntries($allGalleries);

					$product->setIsMassupdate(true);
					$product->setExcludeUrlRewrite(true);

					try{

						$productRepository->save($product);
					} catch (\Exception $e) {	


						echo $e->getMessage();
						die;
					}


				}
				$wholedata['product']['image'] = $image;
				$wholedata['product']['small_image'] = $image;
				$wholedata['product']['thumbnail'] = $image;

				$error[] = $this->saveProductData($this->_customerSession->getCustomerId(), $wholedata);

			}
			$errorMessage = '';

			foreach ($error as $key => $valueErr) {
				if (is_array($valueErr) && isset($valueErr['error']) && $valueErr['error'] == 1) {
					$message = $valueErr['message'];
					$errorMessage .= 'Error in Row ' . ($key+1) . ' - ' . $message . "<br>";
					$sql = "update ".$tableName." set `status` = '2' where id=".$data['id'];
					//echo $sql;
					$this->_connection->query($sql); 
				}else{
					$sql = "update ".$tableName." set `status` = '1' where id=".$data['id'];
					$this->_connection->query($sql); 
					//	echo $sql;
				}
			}
		}
		$sql = "select count(*) as count_rem from ".$tableName." where status = 0 and customer_id= '".$customer_id."'";
		$dataRem = $this->_connection->fetchAll($sql);

		$message = array();
		$message['data'] = $error;
		$message['message'] = 'Progressing...';
		$message['status'] = true;
		$message['size'] = $dataRem[0]['count_rem'];
		if($dataRem[0]['count_rem'] > 0) {
			$message['progress'] = 1;
		}
		else{
			$message['progress'] = 0;
		}

		return $message;

	}

	public function readCSVFile($file_name,$extension) {

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$attrhelper = $objectManager->create('\Webkul\Marketplace\Helper\AttrHelper');

		$target_path = $this->getUploadPath();
		$csvFile = $target_path . $file_name;
		$importingDatas = array();
		if($extension == 'csv'){
			$importingDatas = $this->_csvProcessor->getData($csvFile);
		}else{

			if ( $xlsx = SimpleXLSX::parse($csvFile) ) {
				$importingDatas = $xlsx->rows();

			} else {
				//echo SimpleXLSX::parseError();
				return false;
			}


			//	$parser = $objectManager->create('\Staempfli\Spreadsheet\Model\Parser');

			//	$importingDatas =   $parser->readFile($csvFile);


		}




		$all_count = count($importingDatas);
		if($all_count <= 0){
			return false;	 
		}
		$error = array();
		$updateCustomerArray = [];
		foreach ($importingDatas as $index => $importingData) :
		$str = implode(',', $importingData);
		$str = $this->clean($str);
		if ($str == '') {
			$all_count = $all_count - 1;
			continue;
		}

		if ($index == 0):
		$header_array = $importingData;
		continue;
		endif;
		foreach ($header_array as $key => $column) {
			$column = $this->cleanheader($column);
			$column = trim($column);

			$data_array[strtolower(trim($column))] = $importingData[$key];
		}

		$taxClass = $this->getTaxClass(); 
		$data_array['gst'] = $this->priceclean($data_array['gst']);
		$data_array['gst'] = $this->taxclean($data_array['gst']);


		if(isset($taxClass['GST-'.$data_array['gst']])){
			$data_array['tax_class_id'] = 	$taxClass['GST-'.$data_array['gst']];
		}



		// validation
		$req_array = array('productid','productname','availablestock','brand','price');

		$notexist = false;
		foreach($req_array as $val) {
			if(!isset($data_array[$val]) || strlen($data_array[$val]) < 0){
				$returnArr = [];
				$returnArr['error'] = 1;
				$returnArr['product_id'] = '';
				$returnArr['sku'] = '';
				$returnArr['message'] = strtoupper($val).' is reqired';
				$notexist = true;

				break;
			}
		}


		if($notexist){
			$error[] = $returnArr;
		}else{
			$customer_id = $this->_customerSession->getCustomerId();
			$category_id = $this->getRequest()->getPost('category_id');
			$sku = strtolower($data_array['productid']);
			$data_array['productcode'] = $data_array['productid'];
			$this->saveToDb($customer_id,$category_id,$sku,$data_array);
			continue;
			$attrArr = $data_array;
			foreach($req_array as $val) {
				unset($attrArr[$val]);
			}
			if(isset($attrArr['deliverytime'])){
				unset($attrArr['deliverytime']);
			}				


			$pid = false;
			$wholedata = array();
			$data_array['productid'] = strtolower($data_array['productid']);
			$data_array['productid'] = $data_array['productid'].'_'.$this->_customerSession->getCustomerId();

			if(isset($data_array['productid'])) {

				try {
					$id = $this->_productResourceModel->getIdBySku($data_array['productid']);
					if ($id) {
						$pid = true;
						$wholedata['id'] = $id;



					}else{

					}
				} catch (\Exception $e) {	

				}

			}  


			if(isset($wholedata['id']) && $wholedata['id'] != '' && isset($data_array['productdescription']) && $data_array['productdescription'] == '')	 {
				$data_array['productdescription'] = $data_array['productname'];
			} 
			//$data_array['productdescription'] = $data_array['productname'];

			$wholedata['set'] = 4;
			$wholedata['type'] = 'simple';

			if(!$pid){
				$url_key = $this->createUrlKey( $data_array['productname'], $data_array['productid']);
				$wholedata['product']['url_key'] = $url_key;
			} 

			$wholedata['product']['category_ids'][] = '2';
			$wholedata['product']['category_ids'][] = '43';
			$wholedata['product']['category_ids'][] =$this->getRequest()->getPost('category_id');
			$wholedata['product']['name'] = $data_array['productname'];
			//$wholedata['product']['brand'] = $data_array['brand'];
			$wholedata['product']['short_description'] = $data_array['productdescription'];
			$wholedata['product']['description'] = $data_array['productdescription'];
			$wholedata['product']['sku'] = $data_array['productid'];
			$wholedata['product']['price'] = $this->priceclean($data_array['price']);

			$wholedata['product']['gst'] = $data_array['gst'];

			if(isset($data_array['casnumber'])) {
				$wholedata['product']['casnumber'] = $data_array['casnumber'];
			}

			foreach($attrArr as $attributeCode=>$attributeValue){
				if($attributeCode == 'quantity'){
					$attributeCode = 'quantity_val';
				}
				if($attributeValue != '' 
				   && $attributeCode != 'casnumber' 
				   &&  $attributeCode != 'productdescription' 
				   && $attributeCode != 'image'
				   && $attributeCode != 'special_price'
				  ) {

					$wholedata['product'][$attributeCode] = $attrhelper->createOrGetId($attributeCode, $attributeValue);
				}

			}
			$image = '';
			if(isset($data_array['image']) && $data_array['image'] != ''){
				try {
					$url = $data_array['image'] ;
					$response = $this->fetchUrl($url);
					if($response){
						$path = $this->getImageUploadPath();
						$pathinfo = pathinfo($url); // To get the filename and extension
						if(isset($pathinfo['extension'])) {
							$ext = $pathinfo['extension'];
							$filename = $path.$pathinfo['filename'];
							$img = $response; // get the image from the url
							file_put_contents($filename.'.'.$ext, $img);  
							$image = $filename.'.'.$ext;
						}
					}
				} catch (Exception $e) {

					$image = '';
				}

			}
			if(isset($data_array['deliverytime'])){
				$wholedata['product']['deliverytime'] = $data_array['deliverytime'];
			}

			$wholedata['product']['stock_data']['manage_stock'] = 1;
			$wholedata['product']['stock_data']['use_config_manage_stock'] = 1;

			$wholedata['product']['quantity_and_stock_status']['qty'] = $data_array['availablestock']; // $data_array['qty']
			$wholedata['product']['quantity_and_stock_status']['is_in_stock'] = 1;
			$wholedata['product']['visibility'] = 4;
			// $wholedata['product']['tax_class_id'] = 0;
			$wholedata['product']['product_has_weight'] = 1;
			$wholedata['product']['weight'] = 1;
			$wholedata['product']['meta_title'] =''; 
			$wholedata['product']['meta_keyword'] =''; 
			$wholedata['product']['meta_description'] ='';
			if($image != '' && isset($wholedata['id']) && $wholedata['id'] != ''){
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$product = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
				$allGalleries = $product->getMediaGalleryEntries();
				foreach ($product->getMediaGalleryEntries() as $key => $entry){
					$types = $entry->getData('types');
					if(!empty($types)) {
						unset($allGalleries[$key]);
					}
				}

				$productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');

				$product->setMediaGalleryEntries($allGalleries);
				$productRepository->save($product);

			}
			$wholedata['product']['image'] = $image;
			$wholedata['product']['small_image'] = $image;
			$wholedata['product']['thumbnail'] = $image;

			$error[] = $this->saveProductData($this->_customerSession->getCustomerId(), $wholedata);

		}
		endforeach;
		$errorMessage = '';
		$count = 1;
		foreach ($error as $key => $valueErr) {
			if (is_array($valueErr) && isset($valueErr['error']) && $valueErr['error'] == 1) {

				if($count >  20){
					$errorMessage  = 'Only 20 errors displayed';
					$this->messageManager->addErrorMessage($errorMessage);

					break;	
				}	$count++;
				$message = $valueErr['message'];
				$errorMessage  = 'Error in Row ' . ($key+1) . ' - ' . $message . "   ";
				$this->messageManager->addErrorMessage($errorMessage);

			}
		} 

		if ($errorMessage != '') {
			//  $this->messageManager->addErrorMessage($errorMessage);
		} else {
			$this->messageManager->addSuccess(__(' Data updated Successfully'));
		}  



	}



	private function checkUrlKeyDuplicates($sku, $urlKey, $storeId) {
		$urlKey .= '.html';

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resourceConnection = $objectManager->create('\Magento\Framework\App\ResourceConnection');
		$connection = $resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

		$tablename = $connection->getTableName('url_rewrite');
		$sql = $connection->select()->from(
			['url_rewrite' => $connection->getTableName('url_rewrite')], ['request_path', 'store_id']
		)->joinLeft(
			['cpe' => $connection->getTableName('catalog_product_entity')], "cpe.entity_id = url_rewrite.entity_id"
		)->where('request_path IN (?)', $urlKey)
			//   ->where('store_id IN (?)', $storeId)
			->where('cpe.sku not in (?)', $sku);

		$urlKeyDuplicates = $connection->fetchAssoc($sql);
		if (!empty($urlKeyDuplicates)) {
			return false;
		} else {
			return true;
		}
	}

	public function getTaxClass(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$taxClassObj = $objectManager->create('Magento\Tax\Model\TaxClass\Source\Product');
		$taxClassess = $taxClassObj->getAllOptions();
		foreach($taxClassess as $taxClass){

			if($taxClass['value'] > 0)
				$taxClassArray[$taxClass['label']] = $taxClass['value'];
		}
		return $taxClassArray;

	}


	public function createUrlKey($title, $sku) {
		$url = preg_replace('#[^0-9a-z]+#i', '-', $title);
		$url = ltrim($url,'-');
		$url = rtrim($url,'-');
		$urlKey = strtolower($url);
		// $storeId = (int) $this->_storeManager->getStore()->getStoreId();
		$storeId = 0;


		$isUnique = $this->checkUrlKeyDuplicates($sku, $urlKey, $storeId); 
		if ($isUnique) {
			return $urlKey;
		} else {
			return $urlKey . '-' . time();
		}
	}

	protected function priceclean($string) {
		$string=preg_replace('/[^0-9\.\-]+/','',$string);

		return $string; // Removes special chars.
	}

	protected function taxclean($string) {
		$string = str_replace('%', '', $string);
		$string = trim($string);
		$string=floatval($string);
		if($string > 0 && $string < 1){
			$string = $string * 100;
		}
		return intval($string); // Removes special chars.
	}

	protected function cleanheader($string) {
		$string = str_replace(' ', '', $string); // Replaces all spaces  
		$string = str_replace('%', '', $string); // Replaces all spaces  

		return preg_replace('/[^A-Za-z\-]-/', '', $string); // Removes special chars.
	}

	protected function clean($string) {
		$string = str_replace(' ', '', $string); // Replaces all spaces  

		//	 $string = str_replace('%', '', $string); // Replaces all spaces  

		return preg_replace('/[^A-Za-z\-]-/', '', $string); // Removes special chars.
	}

	protected function getUploadPath() {
		$file_path = $this->_directoryList->getPath(DirectoryList::MEDIA);
		$retailer_path = $file_path . "/uploads/";
		if (!is_dir($retailer_path))
			$this->_ioAdapter->mkdir($retailer_path, 0775);
		return $retailer_path;
	}

	protected function getImageUploadPath() {
		$file_path = $this->_directoryList->getPath(DirectoryList::MEDIA);
		$retailer_path = $file_path . "/uploadimages/";
		if (!is_dir($retailer_path))
			$this->_ioAdapter->mkdir($retailer_path, 0775);
		return $retailer_path;
	}

	public function fetchUrl($uri) {
		$handle = curl_init();
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		$agent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0';

		curl_setopt($handle, CURLOPT_URL, $uri);
		curl_setopt($handle, CURLOPT_POST, false);
		curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($handle, CURLOPT_USERAGENT, $agent);
		$result = parse_url($uri);
		curl_setopt($handle, CURLOPT_REFERER, $result['scheme'].'://'.$result['host']);

		$response = curl_exec($handle);
		$hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		$body     = substr($response, $hlength);
		// If HTTP response is not 200, throw exception
		if ($httpCode != 200) {
			return '';
			throw new \Exception($httpCode);
		}

		return $body;
	}



	public function saveProductData($sellerId, $wholedata)
	{


		$returnArr = [];
		$returnArr['error'] = 0;
		$returnArr['product_id'] = '';

		//	$returnArr['sku'] = $wholedata['product'];
		$returnArr['message'] = '';
		$wholedata['new-variations-attribute-set-id'] = 4;
		$wholedata['product']['attribute_set_id'] = 4;

		$helper = $this->_marketplaceHelperData;
		if (!$this->_registry->registry('mp_flat_catalog_flag')) {
			$this->_registry->register('mp_flat_catalog_flag', 1);
		}

		if (!empty($wholedata['id'])) {
			$wholedata['product']['id'] = $wholedata['id'];
			$mageProductId = $wholedata['id'];
			$editFlag = 1;
			$storeId = $helper->getCurrentStoreId();
			$savedWebsiteIds = $this->_productRepositoryInterface
				->getById(
				$mageProductId
			)->getWebsiteIds();
			if (!isset($wholedata['product']['website_ids'])) {
				$wholedata['product']['website_ids'] = $savedWebsiteIds;
			}
			$this->_eventManager->dispatch(
				'mp_customattribute_deletetierpricedata',
				[$wholedata]
			);
			$wholedata = $this->adminStoreMediaImages($mageProductId, $wholedata);
		} else {
			$mageProductId = '';
			$editFlag = 0;
			$storeId = 0;
			if (!isset($wholedata['product']['website_ids'])) {
				$wholedata['product']['website_ids'][] = $helper->getWebsiteId();
			}

			if(!isset($wholedata['id']) && empty($wholedata['id'])) {

				$wholedata['product']['url_key'] = $wholedata['product']['url_key'] ?? $this->createUrlKey($data_array['productname'], $data_array['productid']);
			}
		}


		if ($mageProductId) {
			$status1 = $helper->getIsProductEditApproval() ?
				SellerProduct::STATUS_DISABLED : SellerProduct::STATUS_ENABLED;
			if ($helper->getIsProductApproval() && !$helper->getIsProductEditApproval()) {
				$sellerProductColls = $this->_mpProductCollectionFactory->create()
					->addFieldToFilter(
					'mageproduct_id',
					$mageProductId
				)->addFieldToFilter(
					'seller_id',
					$sellerId
				);
				foreach ($sellerProductColls as $sellerProductColl) {
					$status1 = !$sellerProductColl->getIsApproved() ?
						SellerProduct::STATUS_DISABLED : SellerProduct::STATUS_ENABLED;
				}
			}
		} else {
			$status1 = $helper->getIsProductApproval() ?
				SellerProduct::STATUS_DISABLED : SellerProduct::STATUS_ENABLED;
		}

		$status = isset($wholedata['status'])?$wholedata['status']:$status1;

		$wholedata['store'] = $storeId;
		/*
				* Marketplace Product save before Observer
			*/

		$this->_eventManager->dispatch(
			'mp_product_save_before',
			[$wholedata]
		);

		$catalogProductTypeId = $wholedata['type'];

		/*
				* Product Initialize method to set product data
			*/
		$catalogProduct = $this->productInitialize(
			$this->_marketplaceProductBuilder->build($wholedata, $storeId),
			$wholedata
		);
		if($wholedata['product']['image'] != '') {
			$imagePath = $wholedata['product']['image'];
			$catalogProduct->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);

		}
		/*for downloadable products start*/

		$catalogProduct = $this->buildDownloadableProduct($catalogProduct, $wholedata);

		/*for downloadable products end*/

		/*for configurable products start*/

		$associatedProductIds = [];

		$resultData = $this->buildConfigurableProduct($catalogProduct, $wholedata);

		$catalogProduct = $resultData['catalogProduct'];
		$associatedProductIds = $resultData['associatedProductIds'];

		/*for configurable products end*/

		$this->_catalogProductTypeManager->processProduct($catalogProduct);

		$set = $catalogProduct->getAttributeSetId();

		$type = $catalogProduct->getTypeId();

		if (isset($set) && isset($type)) {
			$allowedsets = explode(',', $helper->getAllowedAttributesetIds());
			$allowedtypes = explode(',', $helper->getAllowedProductType());
			if (!in_array($type, $allowedtypes) || !in_array($set, $allowedsets)) {
				$returnArr['error'] = 1;
				$returnArr['message'] = __('Product Type Invalid Or Not Allowed');

				return $returnArr;
			}
		} else {
			$returnArr['error'] = 1;
			$returnArr['message'] = __('Product Type Invalid Or Not Allowed');

			return $returnArr;
		}
		if (isset($type) && $type == 'configurable') {
			$allowedtypes = explode(',', $helper->getAllowedProductType());
			if (!in_array('virtual', $allowedtypes) && empty($wholedata['product']['weight'])) {
				$returnArr['error'] = 1;
				$returnArr['message'] = __(
					'You are not allowed to add virtual products, so please set product weight.'
				);

				return $returnArr;
			}
		}

		if ($catalogProduct->getSpecialPrice() == '') {
			$catalogProduct->setSpecialPrice(null);
			$catalogProduct->getResource()->saveAttribute($catalogProduct, 'special_price');
		}

		$originalSku = $catalogProduct->getSku();
		//$catalogProduct->setIsMassupdate(true);
		// $catalogProduct->setExcludeUrlRewrite(true);

		//	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $state = $objectManager->create('\Magento\Framework\App\State');


		$catalogProduct->setStatus($status);
		$this->catalogProductSave = $catalogProduct;
		/*$state->emulateAreaCode(
				'adminhtml', 
				function () { 
				*/			

		try{
			$this->catalogProductSave->save();
		} catch (\Exception $e) {



			if(strpos($e->getMessage(),"key for specified store already") !== false){
				$url_key = $this->createUrlKey($wholedata['product']['name'], $wholedata['product']['productcode']);
				$wholedata['product']['url_key'] = $url_key;
				$this->catalogProductSave->setData('url_key',$url_key);
				$this->catalogProductSave->save();
			}else{
				print_r($e->getMessage());
				die;

			}
		}
		/* }
			); */

		$catalogProduct = $this->catalogProductSave;



		$mageProductId = $catalogProduct->getId();

		$this->handleImageRemoveError($wholedata, $mageProductId);
		$this->getCategoryLinkManagement()->assignProductToCategories(
			$catalogProduct->getSku(),
			$catalogProduct->getCategoryIds()
		);

		/*for configurable associated products save start*/

		$this->saveConfigurableAssociatedProducts($wholedata, $storeId);

		/*for configurable associated products save end*/

		$wholedata['id'] = $mageProductId;
		$this->_eventManager->dispatch(
			'mp_customoption_setdata',
			[$wholedata]
		);

		/* Update marketplace product*/
		$this->saveMaketplaceProductTable(
			$mageProductId,
			$sellerId,
			$status1,
			$editFlag,
			$associatedProductIds
		);




		/*
				* Marketplace Custom Attribute Set Tier Price Observer
			*/
		$this->_eventManager->dispatch(
			'mp_customattribute_settierpricedata',
			[$wholedata]
		);

		/*
				* Marketplace Product Save After Observer
			*/
		$this->_eventManager->dispatch(
			'mp_product_save_after',
			[$wholedata]
		);

		/*
				* Marketplace Product Send Mail Function
			*/
		$this->sendProductMail($wholedata, $sellerId, $editFlag);

		$returnArr['product_id'] = $mageProductId;
		$returnArr['sku'] = $wholedata['product']['sku'];
		$returnArr['productcode'] = $wholedata['product']['productcode'];
		/*
				* Create duplicate product
			*/
		if (isset($wholedata['back']) && $wholedata['back'] === 'duplicate') {
			// get and save product for admin store id
			$storeId = 0;
			$duplicateCatalogProduct = $this->_marketplaceProductBuilder->build($wholedata, $storeId);

			$status1 = $helper->getIsProductApproval() ?
				SellerProduct::STATUS_DISABLED : SellerProduct::STATUS_ENABLED;
			//$status = isset($wholedata['status'])?$wholedata['status']:$status1;

			$newProduct = $this->productCopier->copy($duplicateCatalogProduct);

			// setting stock quantity and status
			$this->updateStockData($newProduct->getId());

			$this->_messageManager->addSuccessMessage(__('You duplicated the product.'));
			$newProductId = $newProduct->getEntityId();
			$wholedata['id'] = $newProductId;
			$editFlag = 0;

			$this->_eventManager->dispatch(
				'mp_customoption_setdata',
				[$wholedata]
			);

			$newAssociatedProductIds = [];

			/* Update marketplace product for duplicate product*/
			$this->saveMaketplaceProductTable(
				$newProductId,
				$sellerId,
				$status1,
				$editFlag,
				$newAssociatedProductIds
			);

			/*
					* Marketplace Custom Attribute Set Tier Price Observer for duplicate product
				*/
			$this->_eventManager->dispatch(
				'mp_customattribute_settierpricedata',
				[$wholedata]
			);

			/*
					* Marketplace Product Save After Observer for duplicate product
				*/
			$this->_eventManager->dispatch(
				'mp_product_save_after',
				[$wholedata]
			);

			/*
					* Marketplace Product Send Mail Function for duplicate product
				*/
			$this->sendProductMail($wholedata, $sellerId, $editFlag);
			$returnArr['product_id'] = $newProductId;
		}

		return $returnArr;
	}

	protected function adminStoreMediaImages($productId, $wholedata, $storeId = 0)
	{
		if (!empty($wholedata['product']['media_gallery'])) {
			$flag = 0;
			foreach ($wholedata['product']['media_gallery']['images'] as $key => $value) {
				if ($value['media_type'] == 'external-video') {
					$flag = 1;
				}
			}
			if ($flag == 1) {
				$catalogProduct = $this->_productRepositoryInterface
					->getById(
					$productId,
					false,
					$storeId
				);
				/*for downloadable products start*/

				$catalogProduct = $this->buildDownloadableProduct($catalogProduct, $wholedata);

				/*for downloadable products end*/
				$catalogProduct->setMediaGallery($wholedata['product']['media_gallery'])->save();
				foreach ($wholedata['product']['media_gallery']['images'] as $key => $value) {
					if ($value['value_id'] == '') {
						unset($wholedata['product']['media_gallery']['images'][$key]);
					}
				}
			}
		}

		return $wholedata;
	}

	/**
			* Get LinkFactory instance.
			*
			* @deprecated
			*
			* @return LinkFactory
		*/
	private function getLinkFactory()
	{
		return $this->linkFactory;
	}

	/**
			* Get Sample Factory.
			*
			* @deprecated
			*
			* @return SampleFactory
		*/
	private function getSampleFactory()
	{
		return $this->sampleFactory;
	}

	/**
			* Product initialize function before saving.
			*
			* @param \Magento\Catalog\Model\Product $catalogProduct
			* @param $requestData
			*
			* @return \Magento\Catalog\Model\Product
		*/
	private function productInitialize(\Magento\Catalog\Model\Product $catalogProduct, $requestData)
	{
		$helper = $this->_marketplaceHelperData;
		$requestProductData = $requestData['product'];
		unset($requestProductData['custom_attributes']);
		unset($requestProductData['extension_attributes']);

		/*
				* Manage seller product Stock data
			*/
		$requestProductData = $this->manageSellerProductStock($requestProductData);

		$requestProductData = $this->normalizeProductData($requestProductData);

		if (!empty($requestProductData['is_downloadable'])) {
			$requestProductData['product_has_weight'] = 0;
		}

		$requestProductData = $this->manageProductCategoryWebsiteData($requestProductData);

		$wasLockedMedia = false;
		if ($catalogProduct->isLockedAttribute('media')) {
			$catalogProduct->unlockAttribute('media');
			$wasLockedMedia = true;
		}

		$requestProductData = $this->manageProductDateTimeFilter($catalogProduct, $requestProductData);

		if (isset($requestProductData['options'])) {
			$productOptions = $requestProductData['options'];
			unset($requestProductData['options']);
		} else {
			$productOptions = [];
		}

		$catalogProduct->addData($requestProductData);

		if ($wasLockedMedia) {
			$catalogProduct->lockAttribute('media');
		}

		if ($helper->getSingleStoreModeStatus()) {
			$catalogProduct->setWebsiteIds([$helper->getWebsiteId()]);
		}

		/*
				* Check for "Use Default Value" field value
			*/
		$catalogProduct = $this->manageProductForDefaultAttribute($catalogProduct, $requestData);

		/*
				* Set Downloadable links if available
			*/
		$catalogProduct = $this->manageProductDownloadableData($catalogProduct, $requestData);

		/*
				* Set Product options to product if exist
			*/
		$catalogProduct = $this->manageProductOptionData($catalogProduct, $productOptions);

		/*
				* Set Product Custom options status to product
			*/
		if (empty($requestData['affect_product_custom_options'])) {
			$requestData['affect_product_custom_options'] = '';
		}

		$catalogProduct->setCanSaveCustomOptions(
			(bool) $requestData['affect_product_custom_options']
			&& !$catalogProduct->getOptionsReadonly()
		);

		return $catalogProduct;
	}

	/**
			* Set Downloadable Data in Product Model.
			*
			* @param \Magento\Catalog\Model\Product $catalogProduct
			* @param array $wholedata
			*
			* @return \Magento\Catalog\Model\Product
		*/
	private function buildDownloadableProduct($catalogProduct, $wholedata)
	{
		if (!empty($wholedata['downloadable']) && $downloadableParamData = $wholedata['downloadable']) {
			$downloadableParamData = $links = $this->getDownloadableParamData($downloadableParamData);

			$catalogProduct->setDownloadableData($downloadableParamData);

			$extension = $catalogProduct->getExtensionAttributes();

			if (isset($downloadableParamData['link']) && is_array($downloadableParamData['link'])) {
				$links = [];
				$links = $this->getDownloabaleLinkData($downloadableParamData, $links);
				$extension->setDownloadableProductLinks($links);
			}
			if (isset($downloadableParamData['sample']) && is_array($downloadableParamData['sample'])) {
				$samples = [];
				$samples = $this->getDownloabaleSampleData($downloadableParamData, $samples);
				$extension->setDownloadableProductSamples($samples);
			}
			$catalogProduct->setExtensionAttributes($extension);
			if ($catalogProduct->getLinksPurchasedSeparately()) {
				$catalogProduct->setTypeHasRequiredOptions(true)->setRequiredOptions(true);
			} else {
				$catalogProduct->setTypeHasRequiredOptions(false)->setRequiredOptions(false);
			}
		}
		return $catalogProduct;
	}

	/**
			* Set Downloadable Data in Product Model.
			*
			* @param \Magento\Catalog\Model\Product $catalogProduct
			* @param array $wholedata
			*
			* @return \Magento\Catalog\Model\Product
		*/
	private function buildConfigurableProduct($catalogProduct, $wholedata)
	{
		$associatedProductIds = [];
		if (!empty($wholedata['attributes'])) {
			$requestProductData = $wholedata['product'];
			$attributes = $wholedata['attributes'];
			$setId = $wholedata['set'];
			$catalogProduct->setAttributeSetId($setId);
			$this->_catalogProductTypeConfigurable->setUsedProductAttributeIds(
				$attributes,
				$catalogProduct
			);

			$extensionAttributes = $catalogProduct->getExtensionAttributes();

			$catalogProduct->setNewVariationsAttributeSetId($setId);
			$configurableOptions = [];

			$extensionAttributes->setConfigurableProductOptions($configurableOptions);

			if (!empty($wholedata['associated_product_ids'])) {
				$associatedProductIds = $wholedata['associated_product_ids'];
			}
			// Get variationsMatrix
			$variationsMatrix = [];
			if (!empty($wholedata['variations-matrix'])) {
				foreach ($wholedata['variations-matrix'] as $key => $value) {
					if (empty($value['weight'])) {
						if (!empty($wholedata['product']['weight'])) {
							$productWeight = $wholedata['product']['weight'];
							$wholedata['variations-matrix'][$key]['weight'] = $productWeight;
						} else {
							$wholedata['variations-matrix'][$key]['product_has_weight'] = 0;
						}
					}
				}
				$variationsMatrix = $wholedata['variations-matrix'];
			}

			if ($associatedProductIds || $variationsMatrix) {
				$this->_variationHandler->prepareAttributeSet($catalogProduct);
			}

			if (!empty($variationsMatrix)) {
				$generatedProductIds = $this->_variationHandler->generateSimpleProducts(
					$catalogProduct,
					$variationsMatrix
				);
				$associatedProductIds = array_merge($associatedProductIds, $generatedProductIds);
			}
			$extensionAttributes->setConfigurableProductLinks(array_filter($associatedProductIds));
			if (!isset($wholedata['affect_configurable_product_attributes'])) {
				$wholedata['affect_configurable_product_attributes'] = '';
			}
			$catalogProduct->setCanSaveConfigurableAttributes(
				(bool) $wholedata['affect_configurable_product_attributes']
			);

			$catalogProduct->setExtensionAttributes($extensionAttributes);
		}
		return ["catalogProduct" => $catalogProduct, "associatedProductIds" => $associatedProductIds];
	}

	private function saveConfigurableAssociatedProducts($wholedata, $storeId)
	{
		$configurations = [];
		if (!empty($wholedata['configurations'])) {
			$configurations = $wholedata['configurations'];
		}

		if (!empty($configurations)) {
			$configurations = $this->_variationHandler
				->duplicateImagesForVariations($configurations);
			foreach ($configurations as $associtedProductId => $associtedProductData) {
				$associtedProduct = $this->_productRepositoryInterface
					->getById(
					$associtedProductId,
					false,
					$storeId
				);
				$associtedProductData = $this->_variationHandler
					->processMediaGallery(
					$associtedProduct,
					$associtedProductData
				);
				$associtedProduct->addData($associtedProductData);
				if ($associtedProduct->hasDataChanges()) {
					$associtedProduct->save();
				}
			}
		}
	}

	/**
			* Set Product Records in marketplace_product table.
			*
			* @param int $mageProductId
			* @param int $sellerId
			* @param int $status
			* @param int $editFlag
			* @param array $associatedProductIds
		*/
	private function saveMaketplaceProductTable(
		$mageProductId,
		$sellerId,
		$status,
		$editFlag,
		$associatedProductIds
	) {
		$savedIsApproved = 0;
		$sellerProductId = 0;
		$helper = $this->_marketplaceHelperData;
		if ($mageProductId) {
			$sellerProductColls = $this->_mpProductCollectionFactory->create()
				->addFieldToFilter(
				'mageproduct_id',
				$mageProductId
			)->addFieldToFilter(
				'seller_id',
				$sellerId
			);
			foreach ($sellerProductColls as $sellerProductColl) {
				$sellerProductId = $sellerProductColl->getId();
				$savedIsApproved = $sellerProductColl->getIsApproved();
			}
			$collection1 = $this->_mpProductFactory->create()->load($sellerProductId);
			$collection1->setMageproductId($mageProductId);
			$collection1->setSellerId($sellerId);
			$collection1->setStatus($status);
			$isApproved = 1;
			if ($helper->getIsProductEditApproval()) {
				$collection1->setAdminPendingNotification(2);
			}
			if (!$editFlag) {
				$collection1->setCreatedAt($this->_date->gmtDate());
				if ($helper->getIsProductApproval()) {
					$isApproved = 0;
					$collection1->setAdminPendingNotification(1);
				}
			} elseif (!$helper->getIsProductEditApproval()) {
				$isApproved = $savedIsApproved;
			} else {
				$isApproved = 0;
			}
			$collection1->setIsApproved($isApproved);
			$collection1->setUpdatedAt($this->_date->gmtDate());
			$collection1->save();
		}

		foreach ($associatedProductIds as $associatedProductId) {
			if ($associatedProductId) {
				$sellerAssociatedProductId = 0;
				$sellerProductColls = $this->_mpProductCollectionFactory->create()
					->addFieldToFilter(
					'mageproduct_id',
					$associatedProductId
				)
					->addFieldToFilter(
					'seller_id',
					$sellerId
				);
				foreach ($sellerProductColls as $sellerProductColl) {
					$sellerAssociatedProductId = $sellerProductColl->getId();
				}
				$collection1 = $this->_mpProductFactory->create()->load($sellerAssociatedProductId);
				$collection1->setMageproductId($associatedProductId);
				if (!$editFlag) {
					/* If new product is added*/
					$collection1->setStatus(SellerProduct::STATUS_ENABLED);
					$collection1->setCreatedAt($this->_date->gmtDate());
				}
				if ($editFlag) {
					$collection1->setAdminPendingNotification(2);
				}
				$collection1->setUpdatedAt($this->_date->gmtDate());
				$collection1->setSellerId($sellerId);
				$collection1->setIsApproved(1);
				$collection1->save();
			}
		}
	}

	/**
			* @param array $requestProductData
			*
			* @return array
		*/
	private function manageSellerProductStock($requestProductData)
	{
		if ($requestProductData) {
			if (isset($requestProductData['quantity_and_stock_status']['qty'])) {
				if ($requestProductData['quantity_and_stock_status']['qty'] < 0) {
					$requestProductData['quantity_and_stock_status']['qty'] = abs(
						$requestProductData['quantity_and_stock_status']['qty']
					);
					$requestProductData['stock_data']['qty'] = $requestProductData['quantity_and_stock_status']['qty'];
				}
			}
			$stockData = isset($requestProductData['stock_data']) ?
				$requestProductData['stock_data'] : [];
			if (isset($stockData['qty']) && (double) $stockData['qty'] > 99999999.9999) {
				$stockData['qty'] = 99999999.9999;
			}
			if (isset($stockData['min_qty']) && (int) $stockData['min_qty'] < 0) {
				$stockData['min_qty'] = 0;
			}
			if (!isset($stockData['use_config_manage_stock'])) {
				$stockData['use_config_manage_stock'] = 0;
			}
			if ($stockData['use_config_manage_stock'] == 1 && !isset($stockData['manage_stock'])) {
				$stockData['manage_stock'] = $this->stockConfiguration
					->getManageStock();
			}
			if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
				$stockData['is_decimal_divided'] = 0;
			}
			$requestProductData['stock_data'] = $stockData;
		}
		return $requestProductData;
	}

	/**
			* @param array $requestProductData
			*
			* @return array
		*/
	private function manageProductCategoryWebsiteData($requestProductData)
	{
		foreach (['category_ids', 'website_ids'] as $field) {
			if (!isset($requestProductData[$field])) {
				$requestProductData[$field] = [
					0=>$this->_marketplaceHelperData->getRootCategoryIdByStoreId()
				];
			}
		}
		foreach ($requestProductData['website_ids'] as $websiteId => $checkboxValue) {
			if (!$checkboxValue) {
				unset($requestProductData['website_ids'][$websiteId]);
			}
		}
		return $requestProductData;
	}

	/**
			* @param Magento/Catalog/Model/Product $catalogProduct
			* @param array $requestProductData
			*
			* @return array
		*/
	private function manageProductDateTimeFilter($catalogProduct, $requestProductData)
	{
		$dateFieldFilters = [];
		$attributes = $catalogProduct->getAttributes();
		foreach ($attributes as $attrKey => $attribute) {
			if ($attribute->getBackend()->getType() == 'datetime') {
				if (array_key_exists($attrKey, $requestProductData) && $requestProductData[$attrKey]!='') {
					$dateFieldFilters[$attrKey] = $this->_dateFilter;
				}
			}
		}
		$inputFilter =  new FilterInput(
			$dateFieldFilters,
			[],
			$requestProductData
		);
		$requestProductData = $inputFilter->getUnescaped();
		return $requestProductData;
	}

	/**
			* @param Magento/Catalog/Model/Product $catalogProduct
			* @param array $requestData
			*
			* @return Magento/Catalog/Model/Product
		*/
	private function manageProductForDefaultAttribute($catalogProduct, $requestData)
	{
		if (!empty($requestData['use_default'])) {
			foreach ($requestData['use_default'] as $attributeCode => $useDefaultState) {
				if ($useDefaultState) {
					$catalogProduct->setData($attributeCode, null);
					if ($catalogProduct->hasData('use_config_'.$attributeCode)) {
						$catalogProduct->setData('use_config_'.$attributeCode, false);
					}
				}
			}
		}
		return $catalogProduct;
	}

	/**
			* @param Magento/Catalog/Model/Product $catalogProduct
			* @param array $requestData
			*
			* @return Magento/Catalog/Model/Product
		*/
	private function manageProductDownloadableData($catalogProduct, $requestData)
	{
		if (!empty($requestData['links'])) {
			$links = $this->_linkResolver->getLinks();

			$catalogProduct->setProductLinks([]);

			$catalogProduct = $this->_productLinks->initializeLinks($catalogProduct, $links);
			$productLinks = $catalogProduct->getProductLinks();

			$linkTypes = [
				'related' => $catalogProduct->getRelatedReadonly(),
				'upsell' => $catalogProduct->getUpsellReadonly(),
				'crosssell' => $catalogProduct->getCrosssellReadonly()
			];
			foreach ($linkTypes as $linkType => $readonly) {
				if (isset($links[$linkType]) && !$readonly) {
					foreach ((array) $links[$linkType] as $linkData) {
						if (empty($linkData['id'])) {
							continue;
						}

						$linkProduct = $this->_productRepositoryInterface->getById($linkData['id']);
						$link = $this->_productLinkFactory->create();
						$link->setSku($catalogProduct->getSku())
							->setLinkedProductSku($linkProduct->getSku())
							->setLinkType($linkType)
							->setPosition(isset($linkData['position']) ? (int)$linkData['position'] : 0);
						$productLinks[] = $link;
					}
				}
			}
			$catalogProduct->setProductLinks($productLinks);
		}
		return $catalogProduct;
	}

	/**
			* @param Magento/Catalog/Model/Product $catalogProduct
			* @param $productOptions
			*
			* @return Magento/Catalog/Model/Product
		*/
	public function manageProductOptionData($catalogProduct, $productOptions)
	{
		if ($productOptions && !$catalogProduct->getOptionsReadonly()) {
			// mark custom options that should to fall back to default value
			$options = $this->mergeProductOptions(
				$productOptions,
				[]
			);
			$customOptions = [];
			foreach ($options as $customOptionData) {
				if (empty($customOptionData['is_delete'])) {
					if (empty($customOptionData['option_id'])) {
						$customOptionData['option_id'] = null;
					}
					if (isset($customOptionData['values'])) {
						$customOptionData['values'] = array_filter(
							$customOptionData['values'],
							function ($valueData) {
								return empty($valueData['is_delete']);
							}
						);
					}
					$customOption = $this->productCustomOption->create(['data' => $customOptionData]);
					$customOption->setProductSku($catalogProduct->getSku());
					$customOptions[] = $customOption;
				}
			}
			$catalogProduct->setOptions($customOptions);
		}
		return $catalogProduct;
	}

	/**
			* Internal normalization
			*
			* @param array $requestProductData
			*
			* @return array
		*/
	private function normalizeProductData(array $requestProductData)
	{
		foreach ($requestProductData as $key => $value) {
			if (is_scalar($value)) {
				if ($value === 'true') {
					$requestProductData[$key] = '1';
				} elseif ($value === 'false') {
					$requestProductData[$key] = '0';
				}
			} elseif (is_array($value)) {
				$requestProductData[$key] = $this->normalizeProductData($value);
			}
		}

		return $requestProductData;
	}

	/**
			* Merge product and default options for product.
			*
			* @param array $productOptions   product options
			* @param array $overwriteOptions default value options
			*
			* @return array
		*/
	public function mergeProductOptions($productOptions, $overwriteOptions)
	{
		if (!is_array($productOptions)) {
			return [];
		}

		if (!is_array($overwriteOptions)) {
			return $productOptions;
		}

		foreach ($productOptions as $index => $option) {
			$optionId = $option['option_id'];

			if (!isset($overwriteOptions[$optionId])) {
				continue;
			}

			foreach ($overwriteOptions[$optionId] as $fieldName => $overwrite) {
				if ($overwrite && isset($option[$fieldName]) && isset($option['default_'.$fieldName])) {
					$productOptions[$index][$fieldName] = $option['default_'.$fieldName];
				}
			}
		}

		return $productOptions;
	}

	private function getDownloadableParamData($downloadableParamData)
	{
		if (isset($downloadableParamData['link']) && is_array($downloadableParamData['link'])) {
			foreach ($downloadableParamData['link'] as $key => $linkData) {
				if ($linkData['link_id'] == 0) {
					$linkData['link_id'] = null;
				}
				$linkData['file'] = $this->_jsonHelper->jsonDecode($linkData['file']);
				$linkData['sample']['file'] = $this->_jsonHelper->jsonDecode($linkData['sample']['file']);
				$downloadableParamData['link'][$key]['link_id'] = $linkData['link_id'];
				$downloadableParamData['link'][$key]['file'] = $linkData['file'];
				$downloadableParamData['link'][$key]['sample']['file'] = $linkData['sample']['file'];
			}
		}
		if (isset($downloadableParamData['sample']) && is_array($downloadableParamData['sample'])) {
			foreach ($downloadableParamData['sample'] as $key => $sampleData) {
				if ($sampleData['sample_id'] == 0) {
					$sampleData['sample_id'] = null;
				}
				$sampleData['file'] = $this->_jsonHelper->jsonDecode($sampleData['file']);
				$downloadableParamData['sample'][$key]['sample_id'] = $sampleData['sample_id'];
				$downloadableParamData['sample'][$key]['file'] = $sampleData['file'];
			}
		}
		return $downloadableParamData;
	}

	/**
			* Get Product Link Data from Post Downloadable Data.
			*
			* @param array $downloadableParamData
			* @param array $links
			*
			* @return array
		*/
	private function getDownloabaleLinkData($downloadableParamData, $links)
	{
		foreach ($downloadableParamData['link'] as $linkData) {
			if (!$linkData || (isset($linkData['is_delete']) && $linkData['is_delete'])) {
				continue;
			} else {
				$links[] = $this->linkBuilder->setData(
					$linkData
				)->build(
					$this->getLinkFactory()->create()
				);
			}
		}
		return $links;
	}

	/**
			* Get Product Sample Data from Post Downloadable Data.
			*
			* @param array $downloadableParamData
			* @param array $sampleData
			*
			* @return array
		*/
	private function getDownloabaleSampleData($downloadableParamData, $samples)
	{
		foreach ($downloadableParamData['sample'] as $sampleData) {
			if (!$sampleData || (isset($sampleData['is_delete']) && (bool) $sampleData['is_delete'])) {
				continue;
			} else {
				$samples[] = $this->sampleBuilder->setData(
					$sampleData
				)->build(
					$this->getSampleFactory()->create()
				);
			}
		}
		return $samples;
	}

	/**
			* @param array  $data
			* @param string $sellerId
			* @param bool   $editFlag
		*/
	private function sendProductMail($data, $sellerId, $editFlag = null)
	{
		$helper = $this->_marketplaceHelperData;

		$customer = $this->customerModel->create()->load($sellerId);

		$sellerName = $customer->getFirstname().' '.$customer->getLastname();
		$sellerEmail = $customer->getEmail();

		if (isset($data['product']) && !empty($data['product']['category_ids'])) {
			$categoriesy = $this->categoryModel->create()->load(
				$data['product']['category_ids'][0]
			);
			$categoryname = $categoriesy->getName();
		} else {
			$categoryname = '';
		}

		$emailTempVariables = [];
		$adminStoremail = $helper->getAdminEmailId();
		$adminEmail = $adminStoremail ?
			$adminStoremail : $helper->getDefaultTransEmailId();
		$adminUsername = $helper->getAdminName();

		$emailTempVariables['myvar1'] = $data['product']['name'];
		$emailTempVariables['myvar2'] = $categoryname;
		$emailTempVariables['myvar3'] = $adminUsername;
		if ($editFlag == null) {
			$emailTempVariables['myvar4'] = __(
				'I would like to inform you that recently I have added a new product in the store.'
			);
		} else {
			$emailTempVariables['myvar4'] = __(
				'I would like to inform you that recently I have updated a  product in the store.'
			);
		}
		$senderInfo = [
			'name' => $sellerName,
			'email' => $sellerEmail,
		];
		$receiverInfo = [
			'name' => $adminUsername,
			'email' => $adminEmail,
		];
		if (($editFlag == null && $helper->getIsProductApproval() == 1)
			|| ($editFlag && $helper->getIsProductEditApproval() == 1)) {
			$this->mpEmailHelper->sendNewProductMail(
				$emailTempVariables,
				$senderInfo,
				$receiverInfo,
				$editFlag
			);
		}
	}

	/**
			* Notify seller when image was not deleted in specific case.
			*
			* @param array $wholedata
			* @param int $mageProductId
			* @return void
		*/
	private function handleImageRemoveError($wholedata, $mageProductId)
	{
		if (isset($wholedata['product']['media_gallery']['images'])) {
			$removedImagesCount = 0;
			foreach ($wholedata['product']['media_gallery']['images'] as $image) {
				if (!empty($image['removed'])) {
					$removedImagesCount++;
				}
			}
			if ($removedImagesCount) {
				$expectedImagesCount = count($wholedata['product']['media_gallery']['images']) - $removedImagesCount;
				$catalogProduct = $this->_productRepositoryInterface->getById($mageProductId);
				if ($expectedImagesCount != count($catalogProduct->getMediaGallery('images'))) {
					$this->_messageManager->addNotice(
						__('The image cannot be removed as it has been assigned to the other image role')
					);
				}
			}
		}
	}

	/**
			* @return CategoryLinkManagementInterface
		*/
	private function getCategoryLinkManagement()
	{
		return $this->categoryLinkManagement;
	}

	/**
			* Update Stock Data of Product
			*
			* @param integer $productId
			* @param integer $qty
			* @param integer $isInStock
		*/
	public function updateStockData($productId, $qty = 0, $isInStock = 0)
	{
		try {
			$scopeId = $this->_stockConfiguration->getDefaultScopeId();
			$stockItem = $this->_stockRegistryProvider->getStockItem($productId, $scopeId);
			if ($stockItem->getItemId()) {
				$stockItem->setQty($qty);
				$stockItem->setIsInStock($isInStock);
				$stockItem->save();
			}
		} catch (\Exception $e) {
			$this->_marketplaceHelperData->logDataInLogger(
				"Controller_Product_SaveProduct updateStockData : ".$e->getMessage()
			);
			$error = $e->getMessage;
		}
	}
}
