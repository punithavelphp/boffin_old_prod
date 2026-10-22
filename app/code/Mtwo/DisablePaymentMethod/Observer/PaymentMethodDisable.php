<?php
namespace Mtwo\DisablePaymentMethod\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class PaymentMethodDisable implements ObserverInterface {
	protected $_customerSession;
	public function __construct(
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\State $state
	) {
		$this->_customerSession = $customerSession;
		$this->state = $state;
	}
	public function execute(Observer $observer) {
	
	 
		$payment_method_code = $observer->getEvent()->getMethodInstance()->getCode();
		if ($this->_customerSession->isLoggedIn()) {
			if ($payment_method_code == 'razorpay' || $payment_method_code == 'banktransfer') {
				$result = $observer->getEvent()->getResult();
				if ($this->_customerSession->isLoggedIn()) {
					$customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
					if ($customerGroupId == 2 || $customerGroupId == 3 || $customerGroupId == 6 || $customerGroupId == 7) {
 					}else{
						$result->setData('is_available', false);   
					}
				}
			}else{
				if ($this->_customerSession->isLoggedIn()) {
					$result = $observer->getEvent()->getResult();
					$customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
					if ($customerGroupId == 3 || $customerGroupId == 8) {
						
						$result->setData('is_available', false);   
					}
					
					if ($customerGroupId == 2){
						$purchaseDepartmentExist = false;
						$currentCustomer =  $this->_customerSession->getCustomer();
						$institute = $currentCustomer->getInstitute();
						$department = $currentCustomer->getDepartment();
						$faculty = $currentCustomer->getFaculty();
						if($institute != '' && $department != '' ) {
							
							$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
							$customerFactory = $objectManager->create('Magento\Customer\Model\Customer');
 							$customers = $customerFactory->getCollection()
								->addAttributeToSelect('*')
								->addAttributeToFilter('institute',$institute)
								->addAttributeToFilter('department',$department)
								->addAttributeToFilter('group_id',array(7)); //purchase dept
							if($customers->getSize()){
								$purchaseDepartmentExist = true;
							}
						}
						if(!$purchaseDepartmentExist){
							$result->setData('is_available', false);
						}
					}
					
					
				}
			}
		}else{
			$result = $observer->getEvent()->getResult();
		 	if($this->state->getAreaCode() == 'adminhtml')	{

			}else{
				 $result->setData('is_available', false);   
			}
		}


	}
}
