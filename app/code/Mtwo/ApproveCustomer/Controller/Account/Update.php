<?php
namespace Mtwo\ApproveCustomer\Controller\Account;
use Magento\Framework\App\Action\Context;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Customer\Model\Session;
  use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Update extends ActionProcessor
{
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
		if($groupID == 3){
			 if (isset($_POST['update'])) {

			   $positions = $_POST['positions'];

			   foreach ($positions as $key => $position) {
				  $index = $position[0];
				  $newPosition = $position[1];
				 $this->helper->updateApproverPosition($index,$newPosition);
			   }
				  echo "Priority Updated Successfully";
				  exit();
		   }
		} else{
			$resultRedirect = $this->resultRedirectFactory->create();
					$resultRedirect->setPath('approvecustomer/account/create'); 
					return $resultRedirect;	
		}
		
		
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}