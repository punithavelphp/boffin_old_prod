<?php

namespace Mtwo\ApproveCustomer\Controller\Index;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Purchasedept extends ActionProcessor
{
    public function execute()
    {

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}