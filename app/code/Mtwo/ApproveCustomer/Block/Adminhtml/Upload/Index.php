<?php


namespace Mtwo\ApproveCustomer\Block\Adminhtml\Upload;

class Index extends \Magento\Framework\View\Element\Template
{
    //protected $_template = 'upload/index.phtml';

    public function __construct(
            \Magento\Backend\Block\Template\Context $context
    ) { 
        parent::__construct($context);
    }

	public function getUploadFormAction() {
		 
        return $this->getUrl('customer/upload/updateform');
    }
}


