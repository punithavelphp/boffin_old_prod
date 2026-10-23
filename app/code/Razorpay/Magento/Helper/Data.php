<?php

namespace Razorpay\Magento\Helper;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order;
use Razorpay\Magento\Model\PaymentMethod;
use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Payment\Model\InfoInterface;
use Razorpay\Magento\Controller\Payment\Webhook;
class Data extends AbstractHelper
{
	public $checkoutSession;
	public $config;
	public $rzpMethod;
	public $paymentMethod;

	protected $scopeConfig;
	protected $_storeManager;


	/**
     * Store key
     */
	const STORE = 'store';

	/**
     * @var OrderRepositoryInterface
     */
	private $order;



	/**
     * StatusAssignObserver constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     */


	public function __construct( Context $context,
								Order $order,
								\Magento\Checkout\Model\Session $checkoutSession,
								\Razorpay\Magento\Model\Config $config,
								\Razorpay\Magento\Model\PaymentMethod $rzpMethod
							   )
	{
		$this->order = $order;
		$this->checkoutSession = $checkoutSession;
		$this->config = $config;
		$this->rzpMethod = $rzpMethod;
		$this->paymentMethod = $rzpMethod;

		parent::__construct($context);
	}




	public function createTransactionNew($order, $paymentData,$amount) {

		$errorCode = '001';

		$transactionId = $paymentData['transactionId'];


		if (!$order) {
			return;
		}
		$comment = '';
		$currentOrderState = $order->getState();



			$payment = $order->getPayment();
			$payment->setMethod('razorpay');
			$payment->save();

		if ($errorCode == '000') {
		


			$order->setState(Order::STATE_PROCESSING)
				->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PAYMENT_REVIEW));
			$order->save();
		} 


		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$transactionBuilder = $objectManager->create('\Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface');

		$trans = $transactionBuilder;

		$transaction = $trans->setPayment($payment)
			->setOrder($order)
			->setTransactionId($transactionId)
			->setFailSafe(true)
			->setAdditionalInformation(
			[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
		)
			//build method creates the transaction and returns the object
			->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT);

 

		$transaction->save();
		$orderId = $order->getIncrementId();
 
		if ($errorCode !== '001') {
			$payment->addTransactionCommentsToOrder(
				$transaction, "Razorpay transaction was not successful."
			);

			$errorMsg = 'Razorpay Transaction Failed !';
			$comment .= "Failed";
			$order->setStatus($order::STATE_PAYMENT_REVIEW);


			$order->cancel();
		} else {
			$payment->addTransactionCommentsToOrder(
				$transaction, "Razorpay transaction is completed successfully."
			);
 
			$order->setStatus(Order::STATE_PROCESSING);
 			$objectManager2 = \Magento\Framework\App\ObjectManager::getInstance();

				/* $invoice = $objectManager2->get('\Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
 				 
						 
 				
				$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
				 
				$invoice->register();
				 
  
				$invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
				$invoice->setTransactionId($transactionId);
				
							 
				$invoice->save(); */
				 			/*	$invoice->setShippingAmount($shippingAmount);
 				$invoice->setShippingTaxAmount($shippingTaxAmount);
 				$invoice->setBaseShippingAmount($shippingAmount);
 				$invoice->setBaseShippingTaxAmount($shippingTaxAmount);

				  $invoice->setSubtotal($invoice->getSubTotal() + $shippingTaxAmount + $shippingAmount);
            $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $shippingTaxAmount + $shippingAmount);
            $invoice->setGrandTotal($invoice->getGrandTotal() + $shippingTaxAmount + $shippingAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $shippingTaxAmount + $shippingAmount); 	*/		 
 			//////	$invoiceSender = $objectManager2->create('Magento\Sales\Model\Order\Email\Sender\InvoiceSenderFactory')->create();
			/////	$invoiceSender->send($invoice);

			 
  
 
			$payment->setTransactionId($transactionId);
			$payment->setParentTransactionId($payment->getTransactionId());

			$payment->setStatus(Webhook::STATUS_APPROVED)
				->setAmountPaid($amount)
				->setLastTransId($transactionId)
				->setTransactionId($transactionId)
				->setIsTransactionClosed(true)
				->setShouldCloseParentTransaction(true);




			$transaction = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH, null, true, ""
												   );
			$transaction->setIsClosed(true);


 
			$comment .= '<br/><b>RazorPay payment successful</b><br/><br/>Pay ID - ' . $transactionId . '<br/><br/>Order ID - ' . $orderId;

 


		}
		$this->addOrderHistory($order, $comment);
		$order->setCanSendNewEmailFlag(true);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objectManager->create('Magento\Sales\Model\OrderNotifier')->notify($order);


 
		$order->save();
		$payment->save();

 	}

	function addOrderHistory($order,$comment){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_orderHistoryFactory = $objectManager->create('Magento\Sales\Model\Order\Status\HistoryFactory');

 
		$history = $_orderHistoryFactory->create()
			->setComment($comment)
			->setEntityName('order')
			->setOrder($order);
		$history->save();
		return true;
	}

	public function updateOrderLinkStatusForPaymentLink($orderId,$post)
	{
  
\Magento\Framework\App\ObjectManager::getInstance()
			->get(\Psr\Log\LoggerInterface::class)->debug('$orderId->'.$orderId);

					
					
		//if($order == ''){
			$order = $this->order->load($orderId);
		//}
		\Magento\Framework\App\ObjectManager::getInstance()
			->get(\Psr\Log\LoggerInterface::class)->debug('$order-order-order-order->'.$order->getIncrementId());
		$order->addStatusHistoryComment(
			__('Initiate createTransaction')
		);

		$payment = $order->getPayment();
		if(isset($post['payload'])) {
			$amountPaid     = $post['payload']['payment']['entity']['amount'];
			$rzpPaymentId    = $post['payload']['payment']['entity']['id'];
		}else if(isset($post['rzp_payment_id'])){
			\Magento\Framework\App\ObjectManager::getInstance()
				->get(\Psr\Log\LoggerInterface::class)->debug('$post amount_paid-->'.$post['amount_paid']);
			$amountPaid     =  $post['amount_paid'];;
			$rzpPaymentId    = $post['rzp_payment_id'];
			\Magento\Framework\App\ObjectManager::getInstance()
				->get(\Psr\Log\LoggerInterface::class)->debug('$rzpPaymentId-->'.$rzpPaymentId);
		}else{
			return;	
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$lastQuoteId = $order->getQuoteId();

			\Magento\Framework\App\ObjectManager::getInstance()
				->get(\Psr\Log\LoggerInterface::class)->debug('$lastQuoteId-->'.$lastQuoteId);


		if((empty($rzpPaymentId) === false) and substr($rzpPaymentId, 0,4) === 'pay_')
		{


			//get razorpay orderLink
			$orderLinkCollection = $objectManager->get('Razorpay\Magento\Model\OrderLink')
				->getCollection()
				->addFieldToSelect('entity_id')
				->addFieldToSelect('order_placed')
				->addFieldToSelect('rzp_order_amount')
				->addFieldToFilter('quote_id', $lastQuoteId)
				->addFieldToFilter('rzp_payment_id', $rzpPaymentId);
				//	->addFieldToFilter('increment_order_id', $order->getIncrementId())
			\Magento\Framework\App\ObjectManager::getInstance()
				->get(\Psr\Log\LoggerInterface::class)->debug('$orderLinkCollection->getSelect()-->'.$orderLinkCollection->getSelect());

				//$orderLinkCollection->getFirstItem();
 			$orderLink = $orderLinkCollection->getFirstItem()->getData();

 

			// 	if (empty($orderLink['entity_id']) === false and !$orderLink['order_placed'])
			{

				\Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('$orderLink22222->'.json_encode($orderLink));
			$paymentAmount = $this->rzpMethod->getAmountPaid($rzpPaymentId);
				$paymentAmount = $paymentAmount/100;
				$amountPaid = number_format($paymentAmount, 2, ".", "");
				 $amountPaid = $orderLink['rzp_order_amount'];
				$amountPaid = $amountPaid/100;
				
\Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('$amountPaid->'.$amountPaid);

				//get the payment action
				$paymentAction = $this->config->getPaymentAction();

				$authOrCapture = ($paymentAction === 'authorize') ? "Authroized" : "Captured";

				$order->addStatusHistoryComment(
					__('Actual Amount %1 of %2, with Razorpay -- applied.', $authOrCapture, $order->getBaseCurrency()->formatTxt($amountPaid))
				);

				//update quote
			 
				$quote = $objectManager->get('Magento\Quote\Model\Quote')->load($lastQuoteId);
			 \Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('$quote 1->'.$quote->getGrandTotal());
	 \Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('$amountPaid 1->'.$amountPaid);
				
				
				
				//validate amount before placing order
				$quoteAmount    = (int) (number_format($quote->getGrandTotal() , 0, ".", ""));
				$rzpOrderAmount = (int) (number_format($amountPaid, 0, ".", ""));
				 \Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('$quoteAmount  Final->'.$quoteAmount);
	 \Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('$rzpOrderAmount Final->'.$rzpOrderAmount);
				
				
				 
				
				$order->addStatusHistoryComment(
					__('Actual Amount %1 == %2, with check applied.', $quoteAmount, $rzpOrderAmount)
				); 
				if ($rzpOrderAmount < $quoteAmount)
				{
					  \Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('-------$quote->Inside processing-->');
					$order->setState('processing')
						->setStatus('payment_review')->save();
				}else{
								 \Magento\Framework\App\ObjectManager::getInstance()
					->get(\Psr\Log\LoggerInterface::class)->debug('-------$quote->Inside createTransaction-->'.$rzpPaymentId);

				 
					$order->addStatusHistoryComment(
						__('Inside createTransaction')
					);
					
					 
					$paymentData['transactionId'] = $rzpPaymentId;
 
 					$this->createTransactionNew($order, $paymentData,$amountPaid);


					$objectManager->get('Razorpay\Magento\Model\OrderLink')->load($orderLink['entity_id'])->setOrderId($order->getId())
						->setOrderPlaced(true)
						->save();
				}


				$quote->setIsActive(false)->save();

				$this->checkoutSession->replaceQuote($quote);

			}
		}
	}

}
