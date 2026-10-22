<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
  use Mtwo\ApproveCustomer\Controller\ActionProcessor;

 class Save extends ActionProcessor
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
		$resultRedirect = $this->resultRedirectFactory->create();
		$response = array();
		$data= $this->getRequest()->getPostValue();
		$this->customerSession->setCustomFormData($data); 
		$resultRedirect->setPath('approvecustomer/account/create');
 		if(isset($data['faculty_type'])) {
			$response = $this->helper->createAccount($data);
			 
			  
			if($response['status']){
				$this->customerSession->unsCustomFormData(); 
				$this->messageManager->addSuccess($response['text']);
				 
				if(isset($response['edit'])) {
					if(isset($response['group_id']) && $response['group_id'] == 2){
						$url = $this->_url->getUrl('approvecustomer/index/faculty');
					}elseif(isset($response['group_id']) && $response['group_id'] == 6){
						$url = $this->_url->getUrl('approvecustomer/index/po');
					}elseif(isset($response['group_id']) && $response['group_id'] == 4){
						$url = $this->_url->getUrl('approvecustomer/index/index');
					}
					else if(isset($response['group_id']) && $response['group_id'] == 5){
						$url = $this->_url->getUrl('approvecustomer/account/approver');
 					}else if(isset($response['group_id']) && $response['group_id'] == 7){
						$url = $this->_url->getUrl('approvecustomer/index/purchasedept/group/purchasedept');
 					}else{
						$url = $this->_url->getUrl('approvecustomer/index/index');
					}
					
					$p =		$this->customerSession->getCurPage();
					$limit =	$this->customerSession->getPageLimit();
  					
					
					$resultRedirect->setUrl($url.'?p='.$p.'&limit='.$limit);
					return $resultRedirect;
				} else{
					$resultRedirect->setPath('approvecustomer/index/index');
				}
			}else{
				$this->messageManager->addError($response['text']);
			}
			if(isset($data['redirect'])) {
				$resultRedirect->setPath('approvecustomer/account/approver');
				// $this->messageManager->addSuccess('Created Successfully.');
				$this->customerSession->setCustomFormData(NULL); 
			}
			
		}
		else if(isset($data['approver_count'])) { 
			$response = $this->helper->updateApprover($data['approver_count']);
			$this->messageManager->addSuccess('Updated Successfully.');
			$this->customerSession->setCustomFormData(NULL); 
			$resultRedirect->setPath('approvecustomer/account/approver'); 
		}else{
			$this->messageManager->addError('Some Error Occurs. Please try again');
		}
		 
		return $resultRedirect;
    }
}