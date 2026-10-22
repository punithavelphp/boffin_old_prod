<?php

namespace Razorpay\Magento\Controller\Index;

use Razorpay\Api\Api;
use Razorpay\Magento\Model\PaymentMethod;
use Magento\Framework\Controller\ResultFactory;

class Payonline extends \Razorpay\Magento\Controller\BaseController
{
	protected $quote;

	protected $checkoutSession;

	protected $cartManagement;

	protected $cache;

	protected $orderRepository;

	protected $logger;
	protected $saleCollection;
	protected $quoteId;
	protected $json;
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Razorpay\Model\Config\Payment $razorpayConfig
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Razorpay\Magento\Model\Config $config,
		\Magento\Quote\Api\CartManagementInterface $cartManagement,
		\Razorpay\Magento\Model\CheckoutFactory $checkoutFactory,
		\Magento\Framework\App\CacheInterface $cache,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Psr\Log\LoggerInterface $logger
	) {
		parent::__construct(
			$context,
			$customerSession,
			$checkoutSession,
			$config
		);

		$this->config          = $config;
		$this->cartManagement  = $cartManagement;
		$this->customerSession = $customerSession;
		$this->checkoutFactory = $checkoutFactory;
		$this->cache = $cache;
		$this->orderRepository = $orderRepository;
		$this->logger          = $logger;
		$this->json 		   = false;
		$this->orderIncrementId = '';
		$this->mobilenumber = '';
		$this->email = '';

		$this->objectManagement   = \Magento\Framework\App\ObjectManager::getInstance();
	}
	public function jsonExecute($orderIncrementId,$mobilenumber='',$email=''){
		$this->json = true;
		$this->orderIncrementId = $orderIncrementId;
		$this->mobilenumber = $mobilenumber;
		$this->email = $email;

		$_GET['order'] = $orderIncrementId;

		return $this->execute();
	}
	public function execute()
	{


		$code = 200;

		if (isset($_GET['order']) && $_GET['order'] != '')
		{
			$this->orderIncrementId = $_GET['order'];
		}
		if($this->orderIncrementId != ''){

			$order_id = $this->orderIncrementId;

			$responseContent = [
				'success'   => true,
				'order_id'  => false,
				'parameters' => []
			];



			# fetch the related sales order and verify the payment ID with rzp payment id
			# To avoid duplicate order entry for same quote
			$saleCollection = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($order_id);

			$this->saleCollection = $saleCollection;
			// $salesOrder = $saleCollection->getData();

			$receipt_id = $this->saleCollection->getQuoteId();
			$this->quoteId = $receipt_id;

			$amount = (int) (number_format($this->getQuote()->getGrandTotal() * 100, 0, ".", ""));
		}
		$maze_version = $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
		$module_version =  $this->_objectManager->get('Magento\Framework\Module\ModuleList')->getOne('Razorpay_Magento')['setup_version'];

		try
		{
			//save to razorpay orderLink
			$orderLinkCollection = $this->_objectManager->get('Razorpay\Magento\Model\OrderLink')
				->getCollection()
				->addFieldToFilter('quote_id', $receipt_id);



			$orderLinkData = $orderLinkCollection->getFirstItem()->getData();


			$createNewOrder = true;

			if (!empty($orderLinkData['entity_id']))
			{
				//	if (( $orderLinkData['rzp_order_amount'] == $amount)  && (isset($orderLinkData['rzp_order_id'])))
				{

					$createNewOrder = false;

				}
			}

			if ($createNewOrder)
			{


				$payment_capture = 1;


				$order = $this->rzp->order->create([
					'amount' => $amount,
					'receipt' => $receipt_id,
					'currency' => 'INR',
					'payment_capture' => $payment_capture,
					'app_offer' => ($this->getDiscount() > 0) ? 1 : 0
				]);




				$orderLnik = $this->_objectManager->create('Razorpay\Magento\Model\OrderLink');
				$orderLnik->setQuoteId($receipt_id)
					->setRzpOrderId($order['id'])
					->setOrderId($this->saleCollection->getId())
					->setIncrementOrderId($this->saleCollection->getIncrementId())
					->setRzpOrderAmount($amount)
					->setEmail($this->saleCollection->getCustomerEmail()) 
					->save();

				$orderLinkData = $orderLnik->getData();



			}
			else
			{


				if (isset($orderLinkData['rzp_order_id']) === true)
				{
					$order = $this->rzp->order->fetch($orderLinkData['rzp_order_id']);
				}
			}



			$responseContent = [
				'message'   => 'Unable to create your order. Please contact support.',
				'parameters' => []
			];

			if (null !== $order && !empty($order['id']))
			{



				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($this->saleCollection->getCustomerId());

				try{
					$notify = array('sms'=>false, 'email'=>false);
					if($this->mobilenumber == ''){
						$this->mobilenumber = '+91'.$customer->getMobilenumber();

					}
					if($this->mobilenumber != ''){
						$notify['sms'] = true;
					}
					if($this->email == ''){
						$this->email =  $this->saleCollection->getCustomerEmail();
					}
					if($this->email != ''){
						$notify['email'] = true;
					}
					$this->logger->info("-------------Razorpay Payment Link Started-------------.");
					$data = array('amount'=>$order->amount, 'currency'=>'INR', 'accept_partial'=>false,
								  'first_min_partial_amount'=>0, 'description' => 'Boffin Sales #'.$this->saleCollection->getIncrementId(), 'reference_id'=>$this->quoteId,'customer' => array('name'=>$this->saleCollection->getCustomerFirstname(),
'email' => $this->email, 'contact'=>$this->mobilenumber),  'notify'=>$notify ,
								  'reminder_enable'=>true ,'notes'=>array( 'boffin_order_id'=> $this->saleCollection->getId(),'boffin_weblink'=> 'WEBLINkCASE'),'callback_url' => 'https://boffinbutler.com/sales/order/view/order_id/'.$this->saleCollection->getId(),
								  'callback_method'=>'get');
					$this->logger->info(json_encode($data));



					$payment_link =	$this->rzp->paymentLink->create($data);
					$this->logger->info("-------------Razorpay Payment Link Generated.-------------");



					$orderLnik->setRzpPaymentId($payment_link->id)->save();
				} catch(\Exception $e) {
					try{
						$payment_link =	$this->rzp->paymentLink->fetch($orderLinkData['rzp_payment_id']);	
					} catch(\Exception $e) {
						$payment_link =	$this->rzp->paymentLink->fetch('?reference_id='.$receipt_id);	
						if($payment_link && isset($payment_link['payment_links']) && isset($payment_link['payment_links'][0])){
							$payment_link = $payment_link['payment_links'][0];
						}
					}

				}

				if(isset($payment_link) && !empty($payment_link->short_url)){
					if($this->json){
						$responseContent = [
							'success' => true,
							'url' => $payment_link->short_url
						];
						$code = 200; 

						return $responseContent;
					}




					header('location:'.$payment_link->short_url);die;
				}else{
					// handle issues

				}
				die;

				$is_hosted = false;

				$merchantPreferences    = $this->getMerchantPreferences();

				$responseContent = [
					'success'           => true,
					'rzp_order'         => $order->id,
					'order_id'          => $receipt_id,
					'amount'            => $order->amount,
					'quote_currency'    => $this->getQuote()->getQuoteCurrencyCode(),
					'quote_amount'      => number_format($this->getQuote()->getGrandTotal(), 2, ".", ""),
					'maze_version'      => $maze_version,
					'module_version'    => $module_version,
					'is_hosted'         => $merchantPreferences['is_hosted'],
					'image'             => $merchantPreferences['image'],
					'embedded_url'      => $merchantPreferences['embedded_url'],
				];

				$code = 200;

				$this->checkoutSession->setRazorpayOrderID($order->id);
				$this->checkoutSession->setRazorpayOrderAmount($amount);

				if (empty($orderLinkData['entity_id']) === false)
				{
					$orderLinkCollection->setRzpOrderId($order->id)
						->setRzpOrderAmount($amount)
						->setEmail($this->saleCollection->getCustomeEmail())
						->save();
				}
				else
				{
					$orderLnik = $this->_objectManager->create('Razorpay\Magento\Model\OrderLink');
					$orderLnik->setQuoteId($receipt_id)
						->setRzpOrderId($order->id)
						->setRzpOrderAmount($amount)
						->setEmail($this->saleCollection->getCustomeEmail())
						->save();
				}

			}
		}
		catch(\Razorpay\Api\Errors\Error $e)
		{
			$responseContent = [
				'message'   => $e->getMessage(),
				'parameters' => []
			];
		}
		catch(\Exception $e)
		{
			$responseContent = [
				'message'   => $e->getMessage(),
				'parameters' => []
			];
		}

		//set the chache for race with webhook
		$this->cache->save("started", "quote_Front_processing_$receipt_id", ["razorpay"], 300);

		$response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		$response->setData($responseContent);
		$response->setHttpResponseCode($code);

		return $response;


	}




	public function getOrderID()
	{
		return $this->checkoutSession->getRazorpayOrderID();
	}

	public function getRazorpayOrderAmount()
	{
		return $this->checkoutSession->getRazorpayOrderAmount();
	}

	protected function getMerchantPreferences()
	{
		try
		{
			$api = new Api($this->config->getKeyId(),"");

			$response = $api->request->request("GET", "preferences");
		}
		catch (\Razorpay\Api\Errors\Error $e)
		{
			echo 'Magento Error : ' . $e->getMessage();
		}

		$preferences = [];

		$preferences['embedded_url'] = Api::getFullUrl("checkout/embedded");
		$preferences['is_hosted'] = false;
		$preferences['image'] = $response['options']['image'];

		if(isset($response['options']['redirect']) && $response['options']['redirect'] === true)
		{
			$preferences['is_hosted'] = true;
		}

		return $preferences;
	}

	public function getDiscount()
	{
		return ($this->getQuote()->getBaseSubtotal() - $this->getQuote()->getBaseSubtotalWithDiscount());
	}

	public function getQuote(){
		return  $this->_objectManager->get('Magento\Quote\Model\Quote')->load($this->quoteId);	
	}
}
