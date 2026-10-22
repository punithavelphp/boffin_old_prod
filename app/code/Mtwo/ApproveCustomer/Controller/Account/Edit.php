<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Edit extends ActionProcessor
{
	protected $customerFactory;
	protected $helperData;

	public function __construct(
		Context $context
		,Data $helper
		, Session $customerSession
	) {
		$this->helper  = $helper;
		$this->customerSession  = $customerSession;
		parent::__construct($context);
	}

	public function execute()
	{ 
		$edit = false;
		$resultRedirect = $this->resultRedirectFactory->create();
		 
		if($this->getRequest()->getParam('customer')){
			$data = $this->helper->getAccount($this->getRequest()->getParam('customer'));
		
 			if($data){
 				$edit = true;
				$p = (isset($_GET['p'])) ? $_GET['p'] : 1;
				$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 5;
				$this->customerSession->setCurPage($p);
				$this->customerSession->setPageLimit($limit);
 				$data['approver_id'] = array();
				$this->customerSession->setCustomFormData($data);
			}

		}
		 
		if($edit === false){
 			$resultRedirect->setPath('approvecustomer/index/index'); 
			// $this->messageManager->addError('Some Error Occurs. Please try again');
			return $resultRedirect;
		}
		$this->_view->loadLayout();
	//	$this->_view->getLayout()->initMessages();
		$this->_view->renderLayout();
	}
}