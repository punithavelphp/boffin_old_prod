<?php

namespace Mtwo\Pdf\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mtwo\Pdf\Helper\Data;

  

class Request extends \Magento\Backend\App\Action
{
  public $helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
         Context $context
        ,\Magento\Framework\View\Result\PageFactory $resultPageFactory
        ,\Magento\Framework\Registry $registry
		, Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
				$this->helper = $helper;

        parent::__construct($context);
    } 
	 
 
	public function execute()
	{
	 	$quote_id = $this->getRequest()->getParam('id');
	 
		if($quote_id){
			$this->helper->generateQuotePdf($quote_id,'I');
			exit;
		}
		 exit;
		}
}


