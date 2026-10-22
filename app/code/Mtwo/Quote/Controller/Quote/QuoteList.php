<?php

namespace Mtwo\Quote\Controller\Quote;
use Mtwo\ApproveCustomer\Controller\ActionProcessor;

class QuoteList extends ActionProcessor
{
    public function execute()
    {

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
     }
}