<?php
namespace Mtwo\Quote\Block\Adminhtml\Renderer;
use Magento\Framework\DataObject;
use Magento\Backend\Block\Context;
use Magento\Customer\Model\CustomerFactory;
class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
	
	public function __construct(Context $context, CustomerFactory $customerFactory, array $data = [])
    {
		$this->customerFactory = $customerFactory;
          parent::__construct($context);
    }
	
	
     public function render(DataObject $row)
    {
        if(empty($row->getCustomerId())){
            return '-';
 		}else{
			$customer = $this->customerFactory->create()->load($row->getCustomerId());
			return $customer->getfirstname()." - ".$customer->getMobilenumber().' <br>'.$customer->getEmail();
		}
    }
	

}