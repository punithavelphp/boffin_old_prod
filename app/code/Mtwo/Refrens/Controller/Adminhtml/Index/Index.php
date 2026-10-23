<?php
	
	public $_coreRegistry;
	public $_invoiceRepository;
	public $_orderRepository;
	public $orderHelper;
	public $helper;
	public $date;
	public $mpSellerModel;
	public $logger;
	public $resourceConnection;
	public $customer;
	public $url;
	public $refrensFactory;
	public $gstStates;

	namespace Mtwo\Refrens\Controller\Adminhtml\Index;
	
	use Magento\Backend\App\Action;
	use Magento\Backend\App\Action\Context;
	use Magento\Framework\View\Result\PageFactory;
	use Magento\Sales\Api\OrderRepositoryInterface;
	use Webkul\Marketplace\Helper\Data as HelperData;
	use Webkul\Marketplace\Model\SellerFactory as MpSellerModel;
	use Magento\Framework\Stdlib\DateTime\DateTime;
	use Magento\Framework\Controller\ResultFactory;
	class Index extends Action
	{
		/**
			* @var PageFactory
		*/
		protected $_resultPageFactory;
		
		
		/**
			* @param Context     $context
			* @param PageFactory $resultPageFactory
		*/
		public function __construct(
			Context $context
			, ResultFactory $resultPageFactory
			, OrderRepositoryInterface $orderRepository
			, \Magento\Framework\Registry $coreRegistry
			, \Webkul\Marketplace\Helper\Orders $orderHelper
			, HelperData $helper
			,\Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
			, MpSellerModel $mpSellerModel
			, DateTime $dateTime
			,\Psr\Log\LoggerInterface  $logger
			, \Magento\Framework\App\ResourceConnection $resourceConnection
			, \Magento\Customer\Model\Customer $customer
			, \Magento\Framework\UrlInterface $url		     
			,\Mtwo\Refrens\Model\RefrensFactory $refrensFactory
			,\PHPCuong\Region\Model\RegionName $regionName
		
		) {
			$this->_resultPageFactory = $resultPageFactory;
			$this->_coreRegistry = $coreRegistry;
			$this->_invoiceRepository = $invoiceRepository;
			$this->_orderRepository = $orderRepository;
			$this->orderHelper = $orderHelper;
			$this->helper = $helper;
			$this->date = $dateTime; 
			$this->mpSellerModel = $mpSellerModel;
			$this->logger = $logger;
			$this->resourceConnection = $resourceConnection;
			$this->customer = $customer;
			$this->url = $url;
			$this->refrensFactory = $refrensFactory;
			$this->gstStates = array();
			parent::__construct($context);
		}
		
		/**
 			*
			* @return \Magento\Framework\View\Result\Page
		*/
		
		public function getGstStates($region_id){
			if(empty($this->gstStates)){
				$connection = $this->resourceConnection->getConnection();
				$tableName = $this->resourceConnection->getTableName('gst_states');
				$query = "Select * FROM " . $tableName."  where 1"; 
				$result = $connection->fetchAll($query); 
				foreach($result as $res){
					$gstStates[$res['region_id']] = $res['code'];
					$this->gstStates[$res['region_id']]  = $res['code'];
				}
			}
			if(isset($this->gstStates[$region_id])){
			 	return $this->gstStates[$region_id];	
			}
			return 0;

		}
		
		
		public function execute()
		{
			
			 
			$invoiceId = $this->getRequest()->getParam('invoice_id');
			$invoiceDataExist = $this->refrensFactory->create()->getCollection()->addFieldToFilter('invoice_id',$invoiceId)->getFirstItem();
 			$recreate = $this->getRequest()->getParam('edit');
			 
			$editId = false;
			if($invoiceDataExist->getId()){
				if($recreate){
					$editId = $invoiceDataExist->getData('refrens_invoice_id');
				}else{
					$pdfUrl = $invoiceDataExist->getData('refrens_pdf_url');
					$date = $invoiceDataExist->getData('refrens_submitted_on');
					$filename = 'invoice_'.$invoiceId.'_'.strtotime($date).'.pdf';
 					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$filename.'"');
 					readfile($pdfUrl);
					exit;
				}
 	  				$this->messageManager->addSuccess(__('Invoice already Exist '));
			 
			}
				
									
			 
 				$refrensData = $this->generateInvoice($editId);
				echo '<pre>-----'.__FILE__.'-->'.__LINE__.'===>';
		print_r($refrensData);
		echo '</pre>';die;
			 
			 	if($refrensData   && isset($refrensData->_id)){
				 
				 
				 $refrensId = $refrensData->_id;
				 $pdfUrl = $refrensData->share->pdf;
				 $refrensLinkUrl = $refrensData->share->link;
					$date = date('Y-m-d H:i:s');
				 $this->refrensFactory->create()
					 ->setData('increment_id',$refrensData->invoiceNumber)
					 ->setData('invoice_id',$invoiceId)
					 ->setData('refrens_invoice_id',$refrensId)
					 ->setData('refrens_pdf_url',$pdfUrl)
					 ->setData('refrens_link_url',$refrensLinkUrl)
					 ->setData('refrens_submitted_on',$date)
					 ->save();
					$filename = 'invoice_'.$invoiceId.'_'.strtotime($date).'.pdf';
					 
 					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$filename.'"');
 					readfile($pdfUrl);
					exit;
					  
			 	}
			
			// $this->_coreRegistry->register('current_order', $order);
		/*	$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
			$resultJson->setData(["message" => ("Success"), "success" => true]);
			return $resultJson; */
			   //  $refererUrl = $this->url->getUrl('*/*/*', ['_current' => true]);

			           $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_url->getUrl('sales/invoice/view', ['invoice_id' => $invoiceId]));
        return $resultRedirect;

 

			 
		}
			public function getSellerDetails($sellerId){
			
			$collection = $this->helper->getSellerCollectionObj($sellerId);
			foreach ($collection as $row) {
				return $row;
			}
		}
		
		
		public function generateInvoice($editId = false){
		 
			$invoiceId = $this->getRequest()->getParam('invoice_id');
		  
			$orderId = $this->getRequest()->getParam('order_id');
			if($invoiceId && $invoiceId != ''){
				$invoice =	$this->_invoiceRepository->get($invoiceId);
				$orderId = $invoice->getOrder()->getId();
			}
			$order = $this->_orderRepository->get($orderId);
			$authUrl = 'https://api.refrens.com/authentication';
			$data = array();
			$data['strategy'] = 'app-secret';
			$data['appSecret'] = 'jWP4GhKYz3uIqd5YDEjmz3g05Pjom0';
			$data['appId'] = 'boffin-butler-pvt-ltd';
			$authKeyArray = $this->curl($authUrl,$data);
		 
			$authKeyArray = json_decode($authKeyArray);
			
			
		 	if(isset($authKeyArray->accessToken) && !empty($authKeyArray->accessToken))
		//	if(1)
			{
			  $accessToken = $authKeyArray->accessToken;
				
				
				 
				$items = $invoice->getAllItems();
				$items_data = array();

				$productIds = array();
				foreach ($items as $item) {								
					 $productIds[] = $item->getProductId();
				}	
				$connection = $this->resourceConnection->getConnection();
				$tableName = $this->resourceConnection->getTableName('marketplace_saleslist');
				$query = "Select * FROM " . $tableName."  where order_id = '".$orderId."' and mageproduct_id = '".implode(',',$productIds)."'"; 
				$result = $connection->fetchAll($query); 
				foreach($result as $res){
					$sellerIdArray[$res['seller_id']] = $res['seller_id'];
				}
				 
				$html_write = '';
				foreach($sellerIdArray as $sellerid) {
					
					$tableName = $this->resourceConnection->getTableName('marketplace_saleslist');
					$query = "Select * FROM " . $tableName."  where order_id = '".$orderId."' and seller_id = '".$sellerid."'"; 
					$result = $connection->fetchAll($query); 
					foreach($result as $res){
						$productBySeller[$res['mageproduct_id']] = $res['mageproduct_id'];
					}
					
					
					$sellerDetails = $this->getSellerDetails($sellerid);
		 
					$sellerStreet  = trim($sellerDetails->getData('address_1')).'';
					$sellerStreet .= trim($sellerDetails->getData('address_2')).''; //<br>
					$sellerStreet .= $sellerDetails->getData('city');
					
 					$sellerGSTregion = $this->getGstStates($sellerDetails->getData('region_id'));
					 
					
					$items = $invoice->getAllItems();
					$items_data = array();
					foreach ($items as $item) {
						if ($item->getOrderItem()->getParentItem()) {
							continue;
						}
						if(isset($productBySeller[$item->getProductId()]))
						{
							
							$gstPercent = ($item->getData('tax_amount')  * 100 ) / $item->getData('base_price')    ;
							$gstPercent = $gstPercent /$item->getData('qty');
							if($gstPercent > 4 && $gstPercent <=6){
								$gstPercent = 5;
							}elseif($gstPercent > 11 && $gstPercent <=13){
								$gstPercent = 12;
							}else if($gstPercent > 14 && $gstPercent <=16){
								$gstPercent = 15;
							}else if($gstPercent > 17 && $gstPercent <= 19){
								$gstPercent = 18;
							}else if($gstPercent > 27 && $gstPercent <= 29){
								$gstPercent = 28;
 							}else if($gstPercent > 29 && $gstPercent <= 37){
								$gstPercent = 36;
							}
							$gstPercent = ceil($gstPercent);
							$items_data[] = array(
							"name" => $item->getData('name'), 
							"hsn"=>$this->getProduct($item->getData('product_id'))->getData('hsncode'),
							"rate" => $item->getData('base_price'), 
							"quantity" => $item->getData('qty'), 
							"gstRate" => $gstPercent
							);
							}else {
							continue;	
						} 
					}
					$billingStreet = '';
					$billing   = $order->getBillingAddress();
					$customerId   = $order->getCustomerId();
					$customer = $this->customer->load($customerId);
					$customer_gstn = $customer->getData('gstin_number');
					 
					if($customer_gstn != '') {
						$customer_gstn = str_replace(' ' , '',$customer_gstn);
					}
					$name = $billing->getData('firstname').' '.$billing->getData('lastname');
 				//	$cname = $billing->getData('firstname').' '.$billing->getData('lastname');
				//	$billingStreet .= $name;
					if(!empty($billing->getData('company'))){
						 $cname =  $billing->getData('company');
 						$billingStreet .=  $cname;
					}
					$shippingStreet = '';
					$shipping   = $order->getShippingAddress();
 					$shippingname = $shipping->getData('firstname').' '.$shipping->getData('lastname');
						
				//	$cname = $shipping->getData('firstname').' '.$shipping->getData('lastname');
					if(!empty($shipping->getData('company'))){
						$cname =  $shipping->getData('company');
						$shippingStreet .=  $cname;
					} 
					
					 
					$shippingStreetAddre = $shipping->getStreet();
					 
					$shippingStreet .= $shippingStreetAddre[0];
					if(isset($shippingStreetAddre[1])){
						  $shippingStreet .= $shippingStreetAddre[1];
					}
					
					$customHeader = array();
					$customHeader[] =  array('_id' => "6501ba77af2db60012b78e2c", 'label' =>"Order Number", 'value' => $order->getIncrementId(), 'key' => "hi6az8nxn0l");
					$customHeader[] =  array('_id' => "6501ba77af2db60012b78e2b", 'label' =>"Order Date", 'value' => date('d, M Y',strtotime($order->getCreatedAt())), 'key' => "l90pei0bs2");
					if(!empty($invoice->getPoNumber())){
 						$customHeader[] =  array('_id' => "6501b332dbcf8b0012a569d9", 'label' =>"Po Number", 'value' => $invoice->getPoNumber(), 'key' => "ighoh4lihsn"); 
					
						if(!empty($invoice->getPoDate())){
							$customHeader[] =  array('_id' => "6501b332dbcf8b0012a569d8", 'label' =>"Po Date", 'value' => date('d, M Y',strtotime($invoice->getPoDate())), 'key' => "tywj1yuwe9g");
						}
					}
					 
					 $billingname = $billing->getName();
					$billingStreetAddre = $billing->getStreet();
					 
					$billingStreet .= $billingStreetAddre[0];
					if(isset($billingStreetAddre[1])){
						  $billingStreet .= $billingStreetAddre[1];
					}
					$billing_region_id = $billing->getData('region_id');
					$billingGSTRegion = $this->getGstStates($billing_region_id);
					$authUrl = 'https://api.refrens.com/businesses/boffin-butler-pvt-ltd/invoices';
					$data = array();
					$boffinPhone = '';
					$boffinEmail = '';
					$customeEmail = '';
					$seller_gstn = $sellerDetails->getGstin();
					$seller_gstn = str_replace(' ' , '',$seller_gstn);
					$invoiceArr = [
					"invoiceTitle" => "TAX INVOICE",
					"invoiceSubTitle" => "",
					"invoiceNumber" => $invoice->getIncrementId(),
					"invoiceDate" => date('Y-m-d', strtotime($invoice->getData('created_at'))),
					"dueDate" => date('Y-m-d', strtotime($invoice->getData('created_at').  ' +15 days')),
					"invoiceType" => "INVOICE",
					"currency" => "INR" ,
					"contact" => [
						"phone" => $sellerDetails->getData('mobilenumber'),
						"email" => "info@boffinbutler.com"
					], 
					"billedTo" => [
						"name" => ucWords($billingname),
						"street" => $billingStreet,
						"pincode" => $billing->getData('postcode'),
						"gstState" => $billingGSTRegion,
						"country" => "IN",
						"panNumber" => "",
						"gstin" => $customer_gstn,
						"phone" => "",
						"email" => $order->getCustomerEmail()
					], 
					"shippedFrom" => [
						"name" => $sellerDetails->getData('shop_title'),
						"street" => $sellerStreet,
						"pincode" => $sellerDetails->getData('postalcode'),
						"gstState" =>  $sellerGSTregion,
						"country" => "IN",
						"panNumber" => $sellerDetails->getData('pan'),
						"gstin" => $seller_gstn,
						"phone" => $sellerDetails->getData('mobilenumber'),
						"email" => ""
					], 
					"shippedTo" => [
						"name" => ucWords($shippingname),
						"street" => $shippingStreet,
						"pincode" => $shipping->getData('postcode'),
						"gstState" => $billingGSTRegion,
						"country" => "IN",
					//	"panNumber" => "",
						"gstin" => $customer_gstn,
				//		"phone" => "",
				//		"email" => $order->getCustomerEmail()
					],
					"billedBy" => [
						"name" => $sellerDetails->getData('shop_title'),
						"street" => $sellerStreet,
						"pincode" => $sellerDetails->getData('postalcode'),
						"gstState" => $sellerGSTregion,
						"country" => "IN",
						"panNumber" => $sellerDetails->getData('pan'),
						"gstin" => $sellerDetails->getGstin(),
						"phone" => $sellerDetails->getData('mobilenumber'),
						"email" => "" 
					], 
				  "email"=> [
						"to" => array(
							"name" =>   $order->getData('customer_firstname').' '.$order->getData('customer_lastname'),
					 	"email" =>  $order->getData('customer_email')
						//	"email" =>  "punithavelkt+ref@gmail.com"
						)
					],
				 
						 "signature" =>$sellerDetails->getData('cloud_sign') ,
			//	  "signature" => "https://s3.ap-south-1.amazonaws.com/refrens.uploads/64ec56c006771c0012cfdc73/652cf8163fb2110012a1c7d2/ref1697445972270.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=AKIAJ2JB7HN7WUT5DNNA%2F20231016%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20231016T084622Z&X-Amz-Expires=900&X-Amz-Signature=fa373a36946a56c7dd145e360985828aabf52cce6e75ae8163b29cffe725cc51&X-Amz-SignedHeaders=host&x-id=GetObject" ,
 					"items" =>  $items_data,
					"customLabels" => [
					"signature" => $sellerDetails->getData('shop_title'),
						"invoiceTitle"=> "Invoice Title",
						"invoiceSubTitle"=> "Invoice Subtitle",
						"invoiceDate"=> "Invoice Date",
						"dueDate"=> "Due Date",
						"invoiceNumber" => "Invoice Number",
						"shippedFrom" => "Shipped From",
"shippedTo" => "Shipped To",
						"totalInWords"=>"Total In Words"
					],
					"customHeaders" =>  ($customHeader),
				//	 "signature" => "https://og.refrens.com/img/64ec56c006771c0012cfdc73/652cf3b12ae76e0012e7bf0f/ref1697444832995.png?fq=eyJ3IjoyNDAsImgiOjEyMCwiYnVja2V0IjoicmVmcmVucy51cGxvYWRzIiwiX192IjoyfQ%3D%3D" 
					]; 
				  if($editId){
					  unset($invoiceArr['email']);
				  }
  				/*	echo '<pre>-----'.__FILE__.'-->'.__LINE__.'===>';
echo '<br>';
print_r(json_encode($invoiceArr));
echo '</pre>'; */
					$refrens_invoice = $this->curl($authUrl,$invoiceArr,$accessToken,$editId);
				/*		echo '<pre>-----'.__FILE__.'-->'.__LINE__.'===>';
 echo '<br>';
print_r(($refrens_invoice));
echo '</pre>'; die;*/
					$refrens_invoice = json_decode($refrens_invoice);
				 	if(isset($refrens_invoice->code) && ($refrens_invoice->code == 400 || $refrens_invoice->code == 402)){
						$mesage = $refrens_invoice->message;
					    $this->messageManager->addErrorMessage(__('An error occurred: %1', $mesage));
						return false;
					}
					$this->logger->debug(json_encode($refrens_invoice));
					 $this->messageManager->addSuccess(__('Created Response : %1', json_encode($refrens_invoice)));
					
					return $refrens_invoice;
				}
			}
			return false;
		}
		
		
		public function curl($url,$data,$authkey = '', $editId = false){
			
			$curl = curl_init();
			$header[] = 'Content-Type: application/json';
			
			if($authkey != '') {
				$header[] = 'Authorization: Bearer '.$authkey;
			}
			$method= 'POST';
			if($editId){
				$url .= '/'.$editId;
				$method = 'PATCH';
			}
		 
			
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => $header
			));
			
			$response = curl_exec($curl);
			
			curl_close($curl);
			return  $response;
			
		}
		
		public function getProduct($productId){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 			$product = $objectManager->get('Magento\Catalog\Model\Product');
			$product->load($productId);
			return $product;
	}
	}
