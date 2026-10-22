<?php

namespace Mtwo\Pdf\Controller\Quote;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mtwo\Pdf\Helper\Data;

 class Request extends Action
{
	/**
     * @var PageFactory
     */
	protected $_resultPageFactory;


	/**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
	public function __construct(
		Context $context
		, PageFactory $resultPageFactory
 		, Data $helper
 
	) {
		$this->_resultPageFactory = $resultPageFactory;
		$this->helper = $helper;
 		parent::__construct($context);
	}
 
	public function execute()
	{
	 	$quote_id = $this->getRequest()->getParam('quote_id');
	 
		if($quote_id){
		$this->helper->generateQuotePdf($quote_id);
			exit;
		}
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Quote Request'));
		return $resultPage;
		}
}


