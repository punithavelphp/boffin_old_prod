<?php

namespace Mtwo\ApproveOrder\Controller\Index;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Orders extends ActionProcessor
{
    public function execute()
    {

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
     }
}