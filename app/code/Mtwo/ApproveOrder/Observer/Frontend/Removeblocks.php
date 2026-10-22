<?php

namespace Mtwo\ApproveOrder\Observer\Frontend;
 
use Magento\Framework\Event\ObserverInterface;
 
class Removeblocks implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       $layout = $observer->getLayout();
  		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 		$_customerSession = $objectManager->get('Magento\Customer\Model\Session');
		if($_customerSession->getCustomer()->getGroupId() == 8) {
	 	$layout->unsetElement('customer-account-navigation-orders-link');
	 	$layout->unsetElement('customer-account-navigation-product-reviews-link');
		}
    }
}
