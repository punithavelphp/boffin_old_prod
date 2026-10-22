<?php 
namespace Mtwo\ApproveOrder\Block;

class CreateCustomerLink  extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
     ) {
         $this->_customerSession = $customerSession;
         parent::__construct($context, $defaultPath, $data);
     }

    protected function _toHtml()
    {    
		
         $responseHtml = null;
        if($this->_customerSession->isLoggedIn()) {

            $customerGroup = $this->_customerSession->getCustomer()->getGroupId();

            if($customerGroup == 2) {
                $responseHtml = parent::_toHtml();
            } else if($customerGroup == '3') {
                $responseHtml = '<li class="nav item"><a href="'.$this->getUrl('approvecustomer/account/create/').'">Create Employees</a></li>';
            } 
        }
        return $responseHtml;
    }
}

?>