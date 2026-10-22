<?php

namespace Mtwo\ApproveCustomer\Controller;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\RequestInterface;

abstract class ActionProcessor extends \Magento\Framework\App\Action\Action
{
	public function dispatch(RequestInterface $request)
	{
 		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 		$customerUrl = $objectManager->get('Magento\Customer\Model\Url');
		$_customerSession = $objectManager->get('Magento\Customer\Model\Session');
 		$loginUrl = $customerUrl->getLoginUrl();
 		if (!$_customerSession->authenticate($loginUrl)) {
			$this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
		}
 		return parent::dispatch($request);
	}
}