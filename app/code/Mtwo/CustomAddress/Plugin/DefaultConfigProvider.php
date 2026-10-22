<?php
namespace Mtwo\CustomAddress\Plugin;

class DefaultConfigProvider
{
    public function afterGetConfig($subject, $result)
    {
         
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$customerSession = $objectManager->get('Magento\Customer\Model\Session');
if($customerSession->isLoggedIn()) {
   // customer login action


		$customerId = $result['customerData']['id'];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
		$customerAddress = array();

		foreach ($customerObj->getAddresses() as $address)
		{
			
			
			if($address->getData('is_active') != 1){
				$customerAddress[$address->getId()] = $address->getData('is_active');
				unset($result['customerData']['addresses'][$address->getId()]);
			}
		} 
}
		 return $result;
    }
}
