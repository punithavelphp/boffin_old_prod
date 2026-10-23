<?php
namespace Mtwo\Refrens\Controller\Adminhtml\Index;
	
	use Magento\Backend\App\Action;
	use Magento\Backend\App\Action\Context;
	use Magento\Framework\View\Result\PageFactory;
	use Magento\Sales\Api\OrderRepositoryInterface;
	use Webkul\Marketplace\Helper\Data as HelperData;
	use Webkul\Marketplace\Model\SellerFactory as MpSellerModel;
	use Magento\Framework\Stdlib\DateTime\DateTime;
	use Magento\Framework\Controller\ResultFactory;
	class Challan extends Action
	{
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
			if($invoiceDataExist->getId() && !empty($invoiceDataExist->getData('refrens_challan_pdf_url'))){
					$pdfUrl = $invoiceDataExist->getData('refrens_challan_pdf_url');
					$date = $invoiceDataExist->getData('refrens_submitted_on');
					$filename = 'challan_'.$invoiceId.'_'.strtotime($date).'.pdf';
 					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$filename.'"');
 					readfile($pdfUrl);
					exit;
			 
 	  			$this->messageManager->addSuccess(__('Invoice already Exist '));

			} 
				
				$refrensId = $invoiceDataExist->getData('refrens_invoice_id');
 				$refrensData = $this->generateChallan($refrensId);
				
  
			 	if($refrensData){
				 
	 
				 
				 $refrensId = $refrensData->_id;
				 $pdfUrl = $refrensData->share->pdf;
				 $refrensLinkUrl = $refrensData->share->link;
					$date = date('Y-m-d H:i:s');
				 $invoiceDataExist
 
					 ->setData('refrens_challan_link_url',$refrensLinkUrl)
					 ->setData('refrens_challan_pdf_url',$pdfUrl)
					 ->setData('refrens_challan_id',$refrensId)
 					 ->save();
				 
	
					$filename = 'challan_'.$invoiceId.'_'.strtotime($date).'.pdf';
					 
 					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$filename.'"');
 					readfile($pdfUrl);
					exit;
					  
			 	}
			
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
		
		
		public function generateChallan($refrensId){
		
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
		 	if(isset($authKeyArray->accessToken) && !empty($authKeyArray->accessToken)) { 
		//	if(1){
			  $accessToken = $authKeyArray->accessToken;
				
				
				$connection = $this->resourceConnection->getConnection();
				$tableName = $this->resourceConnection->getTableName('marketplace_saleslist');
				$query = "Select * FROM " . $tableName."  where order_id = '".$orderId."'"; 
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
							
							$gstPercent = (($item->getData('tax_amount')/$item->getData('qty')) * 100 ) / $item->getData('base_price')    ;
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
							"rate" => (float)$item->getData('base_price'), 
							"quantity" => (int)$item->getData('qty'), 
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
					$name = $billing->getData('firstname').' '.$billing->getData('lastname');
 					$cname = $billing->getData('firstname').' '.$billing->getData('lastname');
					$billingStreet .= $name;
					if(!empty($billing->getData('company'))){
						 $name =  $billing->getData('company');
 						$billingStreet = $name.''.$cname;
					}
					$shippingStreet = '';
					$shipping   = $order->getShippingAddress();
 					$name = $shipping->getData('firstname').' '.$shipping->getData('lastname');
						$shippingStreet .= $name;
					$cname = $shipping->getData('firstname').' '.$shipping->getData('lastname');
					if(!empty($shipping->getData('company'))){
						 $name =  $shipping->getData('company');
 						$shippingStreet .= $cname;
						$shippingStreet = $name.''.$cname;
					}
					$billingStreetAddre = $billing->getStreet();
					$billingStreet .= $billingStreetAddre[0];
					$billing_region_id = $billing->getData('region_id');
					$billingGSTRegion = $this->getGstStates($billing_region_id);
			
					
					
					
					
					
					$authUrl = 'https://api.refrens.com/businesses/boffin-butler-pvt-ltd/invoices';
					$data = array();
					$boffinPhone = '';
					$boffinEmail = '';
					$customeEmail = '';
					$invoiceArr = [
					"invoiceTitle" => "Boffin Butler", 
					"invoiceSubTitle" => "Chem Products", 
					"invoiceNumber" => $invoice->getIncrementId(), 
					"invoiceDate" => date('Y-m-d', strtotime($invoice->getData('created_at'))), 
					"dueDate" => date('Y-m-d', strtotime($invoice->getData('created_at').  ' +15 days')), 
					"invoiceType" => "INVOICE", 
					"billType" => "DELIVERYCHALLAN", 
					"currency" => "INR" ,
					"contact" => [
						"phone" => $sellerDetails->getData('mobilenumber'), 
						"email" => $sellerDetails->getData('email')
						
					], 
					"billedTo" => [
						"name" => $name , 
						"street" => $billingStreet, 
						"pincode" => $billing->getData('postcode'), 
						"gstState" => (int)$billingGSTRegion, 
						"country" => "IN", 
						"panNumber" => "", 
						"gstin" => $customer_gstn, 
						"phone" => "", 
						"email" => $order->getCustomerEmail()
					], 
						
						"billedBy"=>[
						  "name" => $sellerDetails->getData('company'), 
							"street" => $sellerStreet, 
							"pincode" => $sellerDetails->getData('postalcode'), 
							"gstState" => (int)$sellerGSTregion, 
							"country" => "IN", 
							"panNumber" => $sellerDetails->getData('pan'), 
							"gstin" => $sellerDetails->getGstin(), 
							"phone" => $sellerDetails->getData('mobilenumber'), 
							"email" => "" 
						] ,
					 
					  "transportDetails"=>[
						"challanDate"=> date('Y-m-d'),
						"challanNumber"=> "012345",
						"transport"=> "Boffin Delivery"
					],
						"shippedFrom"=>[
						  "name" => $sellerDetails->getData('company'), 
							"street" => $sellerStreet, 
							"pincode" => $sellerDetails->getData('postalcode'), 
							"gstState" => 32, 
							"country" => "IN", 
							"panNumber" => $sellerDetails->getData('pan'), 
							"gstin" => $sellerDetails->getGstin(), 
							"phone" => $sellerDetails->getData('mobilenumber'), 
							"email" => "" 
						] ,
						"shippedTo" => [
					   "_id" => $refrensId, 
					   "name" => "Refrens API Sample", 
					   "country" => "IN", 
					   
						  ] ,
					"items" =>  $items_data, 
					"customLabels" => [
					"signature" => "TESTPUNI" ,
						"invoiceNumber" => "Challan No", 
						"invoiceDate" => "Delivery Challan Date", 
						"billedBy" => "Delivered By", 
						"billedTo" => "Delivered To", 
						"expenseNumber" => "Expense No", 
						"shippedTo" => "Shipped To", 
						"shippedFrom" => "Shipped From", 
						"transport" => "Transport Details" 
					]
					//"signature" => "https://boffinbutler.com/media/avatar/IMG-20220624-WA0013.jpg" 
					]; 
				// echo json_encode($invoiceArr);die;
					$refrens_invoice = $this->curl($authUrl,$invoiceArr,$accessToken);
					

					$refrens_invoice = json_decode($refrens_invoice);
 				
				 	if(isset($refrens_invoice->code) && $refrens_invoice->code == 400){
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
		
		
		public function curl($url,$data,$authkey = ''){
			$this->logger->debug('---START REQUEST----');
			$this->logger->debug(json_encode($data));
						$this->logger->debug('---END REQUEST----');

			$curl = curl_init();
			$header[] = 'Content-Type: application/json';
			
			if($authkey != '') {
				$header[] = 'Authorization: Bearer '.$authkey;
			}
			
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
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
	
	
