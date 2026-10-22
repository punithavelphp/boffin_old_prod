<?php 
namespace Mtwo\ApproveOrder\Block;

class OrderListlink extends \Magento\Framework\View\Element\Html\Link\Current
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
		$html = null;
		if($this->_customerSession->isLoggedIn()) {

			$customerGroup = $this->_customerSession->getCustomer()->getGroupId();

			if($customerGroup == '2' ) {
				$html = parent::_toHtml();
			}  else if($customerGroup == '3' || $customerGroup == '6' || $customerGroup == '5') {
				$currentClass = '';
				if ($this->isCurrent()) {
					$currentClass = 'current';
				}
				$html = '<li class="nav item '.$currentClass.'"><a href="'.$this->getUrl('approveorder/index/index/').'">Employees Order</a></li>';
 



			} 

		}
		return $html;
	}
}

?>