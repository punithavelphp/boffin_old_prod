<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;
class Create extends ActionProcessor
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
		$groupID = $this->customerSession->getCustomer()->getGroupId();
 		//	echo $groupID;die;
		if($groupID == 3){
			/*
			$count = $this->customerSession->getCustomer()->getApproverCount();
			$count =  (int)$count;
			if($count <= 0){
				$this->messageManager->addError('Please update Approver Count and Create Approvers.');
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('approvecustomer/account/approver'); 
				return $resultRedirect;
			}else{
				$cusId = $this->customerSession->getCustomer()->getId();
			 	$approvers_array = $this->helper->getApproverCollection($cusId);
				if(count($approvers_array) < $count){
					$remaining = $count - count($approvers_array) ;
					$this->messageManager->addError('Please  Create remaining '.$remaining.' Approvers');
					$resultRedirect = $this->resultRedirectFactory->create();
					$resultRedirect->setPath('approvecustomer/account/approver'); 
					return $resultRedirect;
				}
			}
			*/
		}
		
		
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}