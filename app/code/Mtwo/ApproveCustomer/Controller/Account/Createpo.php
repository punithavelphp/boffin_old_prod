<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Createpo extends ActionProcessor
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
		$groupID = $this->customerSession->getCustomer()->getGroupId();
		 
		
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}