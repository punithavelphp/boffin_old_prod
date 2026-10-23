<?php

namespace Razorpay\Magento\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Razorpay\Magento\Model\PaymentMethod;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AfterPlaceOrderObserver
 * @package Razorpay\Magento\Observer
 */
class AfterPlaceOrderObserver implements ObserverInterface
{
	public $checkoutSession;
	public $config;
	public $rzpMethod;

	/**
     * Store key
     */
	const STORE = 'store';

	/**
     * @var OrderRepositoryInterface
     */
	private $orderRepository;



	/**
     * StatusAssignObserver constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     */
	public function __construct(
		OrderRepositoryInterface $orderRepository,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Razorpay\Magento\Model\Config $config,
		\Razorpay\Magento\Model\PaymentMethod $rzpMethod
	) {
		$this->orderRepository = $orderRepository;
		$this->checkoutSession = $checkoutSession;
		$this->config = $config;

		$this->rzpMethod = $rzpMethod;
	}

	/**
     * {@inheritdoc}
     */
	public function execute(Observer $observer)
	{ 

		$order = $observer->getOrder();

		/** @var Payment $payment */
		$payment = $order->getPayment();

		$pay_method = $payment->getMethodInstance();

		$code = $pay_method->getCode();

		if($code === PaymentMethod::METHOD_CODE)
		{
			$this->updateOrderLinkStatus($payment);



		}

	}

	/**
     * @param Payment $payment
     *
     * @return void
     */
	private function updateOrderLinkStatus(Payment $payment)
	{

		$order = $payment->getOrder();

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$lastQuoteId = $order->getQuoteId();
		$rzpPaymentId  = $payment->getLastTransId();

		if((empty($rzpPaymentId) === false) and substr($rzpPaymentId, 0,4) === 'pay_')
		{
			//get razorpay orderLink
			$orderLinkCollection = $objectManager->get('Razorpay\Magento\Model\OrderLink')
				->getCollection()
				->addFieldToSelect('entity_id')
				->addFieldToSelect('order_placed')
				->addFieldToSelect('rzp_order_amount')
				->addFilter('quote_id', $lastQuoteId)
				->addFilter('rzp_payment_id', $rzpPaymentId)
				->addFilter('increment_order_id', $order->getRealOrderId())
				->getFirstItem();

			$orderLink = $orderLinkCollection->getData();

			if (empty($orderLink['entity_id']) === false and !$orderLink['order_placed'])
			{

				$amountPaid = number_format($this->rzpMethod->getAmountPaid($rzpPaymentId) / 100, 2, ".", "");

				//get the payment action
				$paymentAction = $this->config->getPaymentAction();

				$authOrCapture = ($paymentAction === 'authorize') ? "Authroized" : "Captured";

				$order->addStatusHistoryComment(
					__('Actual Amount %1 of %2, with Razorpay observer applied.', $authOrCapture, $order->getBaseCurrency()->formatTxt($amountPaid))
				);

				//update quote
				$quote = $objectManager->get('Magento\Quote\Model\Quote')->load($lastQuoteId);

				//validate amount before placing order
				$quoteAmount    = (int) (number_format($quote->getGrandTotal() * 100, 0, ".", ""));
				$rzpOrderAmount = (int) (number_format($orderLink['rzp_order_amount'], 0, ".", ""));

				if ($quoteAmount !== $rzpOrderAmount)
				{
					$order->setState('processing')
						->setStatus('payment_review');
				}

				$order->save();

				$quote->setIsActive(false)->save();

				

				//update razorpay orderLink
				$orderLinkCollection->setOrderId($order->getEntityId())
					->setOrderPlaced(true)
					->save();
				
				$orderId = $order->getEntityId();
 \Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->debug('$order->getEntityId()->'.$order->getEntityId());

				 \Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->debug('$rzpPaymentId->'.$rzpPaymentId);


				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

				$post_data['rzp_payment_id'] = $rzpPaymentId;
				$paymentData['transactionId'] = $rzpPaymentId;
				$post_data['amount_paid'] = $amountPaid;
		 // 		$objectManager->get('Razorpay\Magento\Helper\Data')->createTransactionNew($order,$paymentData,$amountPaid);
 				$this->checkoutSession->replaceQuote($quote);
			}
		}
	}



}
