<?php

namespace Mtwo\ApproveOrder\Controller\Index;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;
class Approve extends ActionProcessor
{
	protected $orderFactory;

	public function __construct(
		Context $context,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Mtwo\ApproveOrder\Helper\Email $emailHelper,
		\Magento\Customer\Model\Session $customerSession,
		\Mtwo\Approver\Model\Approved $approved

	) {
		$this->orderFactory = $orderFactory;
		$this->emailHelper = $emailHelper;
		$this->customerSession = $customerSession;
		$this->approved = $approved;

		parent::__construct($context);
	}

	public function execute()
	{
		$orderId = $this->getRequest()->getParam('order');
		$status = $this->getRequest()->getParam('status');
		$p = $this->getRequest()->getParam('p');
		$limit = $this->getRequest()->getParam('limit');
		$order = $this->orderFactory->create()->load($orderId);
		 
 		if($status == 1 || $status == 3){
			$customer_id = $this->customerSession->getCustomer()->getId();
			if($this->customerSession->getCustomer()->getGroupId() == 5){
				$orderIds = $this->customerSession->getApproveOrder();
				foreach($orderIds as $oid){
					if($oid == $orderId){
						$this->approved->setManagerId($customer_id)->setOrderId($oid)->save();
						// $this->emailHelper->generateAndSendPaymentLink($order);
					}
				}
				$all_approvers = $this->customerSession->getAllApprovers();
				if($all_approvers && isset($all_approvers[$orderId])) {
					$approved_data = $this->approved->getCollection()
						->addFieldToFilter('manager_id',array('in'=>array_values($all_approvers[$orderId])))
						->addFieldToFilter('order_id',$orderId)
						;
					foreach($approved_data as $approved_d){
						unset($all_approvers[$orderId][$approved_d->getManagerId()]);
					}
					if(isset($all_approvers[$orderId]) && count($all_approvers[$orderId])){
					}else{
						$order->setData('order_approval_status','1')->save();
						$this->emailHelper->generateAndSendPaymentLink($order);
					}
				}
				// 	$this->emailHelper->generateAndSendPaymentLink($order);
				// $this->emailHelper->sendOrderApproveEmail($order);
			}else{
				
				 
 						// TEST	$this->emailHelper->generateAndSendPaymentLink($order);die;
				$orderIds = $this->customerSession->getApproveOrder();
				if(empty($orderIds)){
					$this->messageManager->addError(
						__('Some error Occurs.')
					);
				}else{
					foreach($orderIds as $order_id){
						if($order_id == $orderId){
							$order->setData('order_approval_status','1')->save();
							$this->emailHelper->generateAndSendPaymentLink($order);
							if($status == 3){
							\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Mtwo\Approver\Model\FacultyApproval::class)->setOrderId($order->getId())->setIncrementId($order->getIncrementId())->save();
							}

						}
					}
				}


			}
			if(!empty($orderIds)){
				$this->messageManager->addSuccess(
					__('Order  Approved Successfully.')
				);
			}



		}else if($status == 2){
			$order->setData('order_approval_status',2)->save();
			$this->messageManager->addSuccess(
				__('Order Denied Successfully.')
			);
			$this->emailHelper->sendOrderDenyEmail($order);
		}

		$resultRedirect = $this->resultRedirectFactory->create();
		$params = array('p' => $p,'limit'=>$limit);

		// $resultRedirect->setPath('approveorder/index/index', $params);
		$url = $this->_url->getUrl('approveorder/index/index');
		$resultRedirect->setUrl($url.'?p='.$p.'&limit='.$limit);
		//header('Location:'.$url.'?p' => $p,'limit'=>$limit);exit;

		return $resultRedirect;
	}
}