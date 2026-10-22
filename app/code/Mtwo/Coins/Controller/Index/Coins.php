<?php

namespace Mtwo\Coins\Controller\Index;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class Coins extends ActionProcessor
{
    public function execute()
    {

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
     }
}