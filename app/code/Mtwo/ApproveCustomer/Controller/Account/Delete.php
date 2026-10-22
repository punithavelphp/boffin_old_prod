<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Delete extends ActionProcessor
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
			$data = $this->helper->deleteApprover($this->getRequest()->getParam('del'));
			 $customer_id = $this->getRequest()->getParam('customer');
 				$p = (isset($_GET['p'])) ? $_GET['p'] : 1;
				$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 5;
				header('location:https://boffinbutler.com/approvecustomer/account/edit/customer/'.$customer_id.'/?p='.$p.'&limit='.$limit);
			 die;
			
		}
	 
          $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}