<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
  use Mtwo\ApproveCustomer\Controller\ActionProcessor;

 class Savepo extends ActionProcessor
{
	public $helper;
	public $customerSession;

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
		$resultRedirect->setPath('approvecustomer/account/createpo');
 		if(isset($data['faculty_type'])) {
			$response = $this->helper->createAccount($data);
			if($response['status']){
				$this->customerSession->unsCustomFormData(); 
				$this->messageManager->addSuccess($response['text']);
		if(isset($response['edit'])) {
			$url = $this->_url->getUrl('approvecustomer/index/po');
					$p =		$this->customerSession->getCurPage();
			$limit =	$this->customerSession->getPageLimit();
 			$resultRedirect->setUrl($url.'?p='.$p.'&limit='.$limit);
			//header('Location:'.$url.'?p' => $p,'limit'=>$limit);exit;
 			return $resultRedirect;
			} else{
			$resultRedirect->setPath('approvecustomer/index/po');
		}
				
				
				
			}else{
				$this->messageManager->addError($response['text']);
			}
			if(isset($data['redirect'])) {
				$resultRedirect->setPath('approvecustomer/account/approver');
				$this->customerSession->setCustomFormData(NULL); 
				// $this->messageManager->addSuccess('Created Successfully.');
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