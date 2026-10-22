<?php

namespace Mtwo\ApproveOrder\Plugin;
use \Magento\Customer\Model\Session;
class OrderViewAuthorizationPlugin
{


	public function __construct(
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Sales\Model\Order\Config $orderConfig
	) {
		$this->customerSession = $customerSession;
		$this->orderConfig = $orderConfig;
	}

	public function aroundCanView(\Magento\Sales\Controller\AbstractController\OrderViewAuthorization $subject,callable $proceed, \Magento\Sales\Model\Order $order)
	{

		$check = $proceed($order);
		if($check){
			return $check; 
		}else{
			$availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();
			$approver_owner_id = 'NULL'; // dummy data
			$orderCustomerInstitute = 'ABC';
			$orderCustomerDepartment = 'ABC';
			$customerInstitute = 'CBA';
			$customerDepartment= 'CBA';
			$customerId = $this->customerSession->getCustomerId();
			$customerGroupId = $this->customerSession->getCustomer()->getGroupId();
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			if ($order->getId()
				&& $order->getCustomerId()
				&& in_array($order->getStatus(), $availableStatuses, true)
			   ) {
				$_customer = $objectManager->get('Magento\Customer\Model\Customer');
				$order_owner = $_customer->load($order->getCustomerId());
				$order_owner_id = $_customer->load($order->getCustomerId())->getFaculty();
				if($customerGroupId == 5 || $customerGroupId == 6){
					$approver_owner_id = $this->customerSession->getCustomer()->getFaculty();
				}

				if($customerGroupId == 7){
					$approver_owner_id = $this->customerSession->getCustomer()->getFaculty();
					$customerInstitute = $this->customerSession->getCustomer()->getInstitute();
					$customerDepartment = $this->customerSession->getCustomer()->getDepartment();
					$orderCustomerInstitute = $order_owner->getInstitute();
					$orderCustomerDepartment = $order_owner->getDepartment();
				}

				if ($order->getId()
					&& $order->getCustomerId()
					&& ( $order->getCustomerId() == $customerId 
						||  $approver_owner_id == $order_owner_id 
						|| $customerId == $order_owner_id 
						|| ($orderCustomerInstitute = $customerInstitute && $orderCustomerDepartment == $customerDepartment)
					   )
					&& in_array($order->getStatus(), $availableStatuses, true)
				   ) {
					return true;
				}
			}

		}

		return false;
	}

}
