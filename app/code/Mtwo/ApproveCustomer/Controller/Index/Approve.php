<?php

namespace Mtwo\ApproveCustomer\Controller\Index;
use Magento\Framework\App\Action\Context;
use Mageplaza\CustomerApproval\Helper\Data;
use Mageplaza\CustomerApproval\Model\Config\Source\TypeAction;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;
class Approve extends ActionProcessor
{
	protected $customerFactory;
	protected $helperData;
    public function __construct(
    Context $context,
    \Magento\Customer\Model\CustomerFactory $customerFactory,
		Data $helper
    ) {
        $this->customerFactory = $customerFactory;
		$this->helperData = $helper;
        parent::__construct($context);
    }
	
    public function execute()
    {
		$customerId = $this->getRequest()->getParam('customer');
		$status = $this->getRequest()->getParam('status');
		$p = $this->getRequest()->getParam('p');
		$limit = $this->getRequest()->getParam('limit');
		$customer = $this->customerFactory->create()->load($customerId);
		if($status == 'approved'){
			 $this->helperData->approvalCustomerById($customerId, TypeAction::EDITCUSTOMER);  
			
 		}else if($status == 'notapproved'){
			$this->helperData->notApprovalCustomerById($customerId);
			 
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		
		$url = $this->_url->getUrl('approvecustomer/index/index');
		$resultRedirect->setUrl($url.'?p='.$p.'&limit='.$limit);
		/*
		$params = array('p' => $p,'limit'=>$limit);
        $resultRedirect->setPath('approvecustomer/index/index',['params' => $params]); */
		return $resultRedirect;
    }
}