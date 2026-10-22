<?php

namespace Mtwo\ApproveCustomer\Controller\Account;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;
class Approver extends ActionProcessor  
{ 

	public function execute()
	{ 

		$this->_view->loadLayout();
		$this->_view->getLayout()->initMessages();
		$this->_view->renderLayout();
	}
 
}