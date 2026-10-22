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

namespace Webkul\Marketplace\Controller\Order;

/**
 * Webkul Marketplace Order Invoice Controller.
 */
class Delivered extends \Webkul\Marketplace\Controller\Order
{
	/**
     * Marketplace order invoice controller.
     *
     * @return \Magento\Framework\View\Result\Page
     */
	public function execute()
	{
		
		
	 
		$helper = $this->helper;
		$isPartner = $helper->isSeller();
		if ($isPartner == 1) {
			if ($order = $this->_initOrder()) {
			 	$this->doDeliveredExecution($order);
				$this->doAdminDeliveredExecution($order);
				 
 				return $this->resultRedirectFactory->create()->setPath(
					'*/*/view',
					[
						'id' => $order->getEntityId(),
						'_secure' => $this->getRequest()->isSecure(),
					]
				);
			} else {
				return $this->resultRedirectFactory->create()->setPath(
					'*/*/history',
					['_secure' => $this->getRequest()->isSecure()]
				);
			}
		} else {
			return $this->resultRedirectFactory->create()->setPath(
				'marketplace/account/becomeseller',
				['_secure' => $this->getRequest()->isSecure()]
			);
		}
	}

	protected function doDeliveredExecution($order)
	{
		try {
			$helper = $this->helper;
			$sellerId = $this->_customerSession->getCustomerId();
			$orderId = $order->getId();
			if ($order->canUnhold()) {
				$this->messageManager->addError(
					__('Can not create invoice as order is in HOLD state')
				);
			} else {

				$trackingcol1 = $this->mpOrdersModel->create()
					->getCollection()
					->addFieldToFilter(
					'order_id',
					$orderId
				)
					->addFieldToFilter(
					'seller_id',
					$sellerId
				);
				foreach ($trackingcol1 as $row) {
					// PUNI CODE STARTS HERE
					$shipmentIds = $row->getShipmentId();
					if ($row->getShipmentId()) {
						$shipmentIdArray = explode(',',$shipmentIds);
						foreach($shipmentIdArray as $shipmentId){
							if ($shipmentId && $shipmentId > 0) {
								$row->setOrderStatus('delivered');
								$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
								$repository = $objectManager->create('\Magento\Sales\Api\ShipmentRepositoryInterface');
								$shipment = $repository->get($shipmentId);
								$objectManager->create(
									\Mtwo\Pdf\Model\DeliveryEmail::class
								)->send($shipment);
								$customer = $objectManager->create('Magento\Customer\Model\Customer')
    ->load($order->getCustomerId());

								$mobile = $customer->getData('mobilenumber');
								 
								// $mobile = '9786979479';
								$message = 'Your order number '.$shipment->getIncrementId().'. has been delivered. We will be happy to hear about experience please give your feedback using (link). Happy to serve you again Boffin Buttler team	';
								  $objectManager->create('\Emc\Otp\Model\Sms')->sendSmsOthers($mobile,$message,'delivery');
 							} 
						}
						$row->save();
					}

				}
			}
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->messageManager->addError($e->getMessage());
		} catch (\Exception $e) {
			$this->helper->logDataInLogger(
				"Controller_Order_Invoice doDeliveredExecution : ".$e->getMessage()
			);
			$this->messageManager->addError(
				__('We can\'t save the delivery right now.')
			);
			$this->messageManager->addError($e->getMessage());
		}
	}

	protected function doAdminDeliveredExecution($order)
	{
		try {


			$isAllItemDelivered = $this->isAllItemDelivered($order->getId());
 			 
			if (1 || $isAllItemDelivered) { 
		 		$order->setStatus('delivered')->setState('complete')->save();
				// Add Points to the Customer here
				$customer_id = $order->getCustomerId();
				
							    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

			$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($customer_id);
				
				$coinsCollection = $objectManager->create('Mtwo\Coins\Model\CoinsFactory')->create()->getCollection()->addFieldToFilter('customer_id',$customer_id)->addFieldToFilter('order_id',$order->getId())->addFieldToFilter('is_debit',0);
		 	  $coinsCollection->getSelect();
				 
				
				
				if($coinsCollection->getSize()){
				}else{
					
				//	$coins = number_format(round($order->getGrandTotal()*1,PHP_ROUND_HALF_UP )/100,0);
					  $coins = round(($order->getGrandTotal()*1)/100);
					
					$objectManager->create('Mtwo\Coins\Model\CoinsFactory')->create()
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
			//	$bb_coins = 0;
			//	 $coinsCollection = $objectManager->create('Mtwo\Coins\Model\CoinsFactory')
			//		 ->create()->getCollection()->addFieldToFilter('customer_id',$customer_id)
			//		 ->addFieldToSelect('customer_id')
			//		  ->addExpressionFieldToSelect('bb_coins', 'SUM({{coins}})', 'coins');
			//		 ;
			//	$coinsCollection->getSelect()->group('customer_id');
			//	 if($coinsCollection->getSize())
				// {
				//	 $customer->setData('bb_coins',$coinsCollection->getFirstItem()->getData('bb_coins'))->save();
				// }
				 
				
			}

		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->helper->logDataInLogger(
				"Controller_Order_Invoice doAdminShippingInvoiceExecution : ".$e->getMessage()
			);
		} catch (\Exception $e) {
			$this->helper->logDataInLogger(
				"Controller_Order_Invoice doAdminShippingInvoiceExecution : ".$e->getMessage()
			);
		}
	}


	protected function isAllItemDelivered($orderId)
	{

		$order_collection = $this->mpOrdersModel->create()
			->getCollection()
			->addFieldToFilter(
			'order_id',
			$orderId
		);


		$flag = 1;
		foreach ($order_collection as $item) {
			if ( $item->getOrderStatus() != 'delivered') {
				$flag = 0;
			}
		}

		return $flag;
	}



}
