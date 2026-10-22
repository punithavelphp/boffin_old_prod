<?php

namespace Mtwo\Analytics\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Customer;
use Mtwo\Coins\Model\CoinsFactory;

class OrderStatusChange implements ObserverInterface
{
	protected $orderRepository;
	protected $transportBuilder;
	protected $inlineTranslation;
	protected $storeManager;

	public function __construct(
		OrderRepositoryInterface $orderRepository,
		TransportBuilder $transportBuilder,
		StateInterface $inlineTranslation,
		StoreManagerInterface $storeManager,
		Customer $customer,
		CoinsFactory $coinsFactory
	) {
		$this->orderRepository = $orderRepository;
		$this->transportBuilder = $transportBuilder;
		$this->inlineTranslation = $inlineTranslation;
		$this->storeManager = $storeManager;
		$this->customer = $customer;
		$this->coinsFactory = $coinsFactory;
	}

	public function execute(Observer $observer)
	{
		/** @var Order $order */
		$order = $observer->getEvent()->getOrder();

		// Get previous and current statuses
		$origOrder = $this->orderRepository->get($order->getId());
		$oldStatus = $origOrder->getOrigData('status');
		$newStatus = $order->getStatus();
		\Magento\Framework\App\ObjectManager::getInstance()
			->get(\Psr\Log\LoggerInterface::class)->debug('-------newStatus->New Status-->'.$newStatus);
		$this->saveBBCoins($order);
		// If status has changed, send notification
		//	if ($newStatus == 'delivered' &&  $oldStatus !== $newStatus) {
		//  $this->sendEmailNotification($order);
		//   $this->sendSmsNotification($order);
		// }
	}

	private function saveBBCoins($order){

		$customer_id = $order->getCustomerId();
		$customer = $this->customer->load($customer_id);
		$coinsCollection = $this->coinsFactory->create()->getCollection()->addFieldToFilter('customer_id',$customer_id)->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('is_debit',0);
		//$coinsCollection->getSelect();
		if($coinsCollection->getSize()){
		}else{
			//$coins = number_format(round($order->getGrandTotal()*5,PHP_ROUND_HALF_UP )/100,0);
			$coins = round(($order->getGrandTotal()*1)/100);
			$this->coinsFactory->create()
				->setData('customer_id',$customer_id)
				->setData('order_id',$order->getId())
				->setData('order_increment_id',$order->getIncrementId())
				->setData('coins',$coins)
				->setData('code','REWARD')
				->setData('comments','Credited for order '.$order->getIncrementId())
				->setData('is_approved',1)
				->setData('approved_by',"SYSTEM")
				->save()
				;

		}

	}

	private function sendEmailNotification(Order $order)
	{
		$this->inlineTranslation->suspend();

		$storeId = $this->storeManager->getStore()->getId();
		$customerEmail = $order->getCustomerEmail();
		$customerName = $order->getCustomerName();

		// Email template variables
		$templateVars = [
			'order' => $order,
			'store' => $this->storeManager->getStore(),
			'customer_name' => $customerName,
			'order_status' => $order->getStatus()
		];

		// Prepare email
		$transport = $this->transportBuilder
			->setTemplateIdentifier('order_status_change_email_template') // Define in admin or email templates
			->setTemplateOptions([
				'area' => 'frontend',
				'store' => $storeId
			])
			->setTemplateVars($templateVars)
			->setFromByScope('sales') // Define sender in Stores > Configuration
			->addTo($customerEmail, $customerName)
			->getTransport();

		$transport->sendMessage();
		$this->inlineTranslation->resume();
	}

	private function sendSmsNotification(Order $order)
	{
		$customerPhone = $order->getBillingAddress()->getTelephone();

		if ($customerPhone) {
			// Replace with your SMS gateway API call
			$message = sprintf(
				"Dear %s, your order #%s status has changed to '%s'.",
				$order->getCustomerName(),
				$order->getIncrementId(),
				$order->getStatus()
			);

			// Example SMS Gateway integration (pseudo-code)
			// $this->smsGateway->sendSms($customerPhone, $message);
		}
	}
}
