<?php

namespace Magelearn\Customform\Block;

/**
 * Customform content block
 */
class Customform extends \Magento\Framework\View\Element\Template
{
    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		        \Mtwo\Brandimage\Block\Adminhtml\Brandimage\Grid $brand,
        array $data = []
    ) {
		$this->brand = $brand;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Post Your Enquiry'));
        
        return parent::_prepareLayout();
    }
	
	public function getBrands(){
		return $this->brand->getOptionArray1();
	}
}
