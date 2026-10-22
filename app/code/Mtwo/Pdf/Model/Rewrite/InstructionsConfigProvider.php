<?php

namespace Mtwo\pdf\Model\Rewrite;
 
class InstructionsConfigProvider extends \Magento\OfflinePayments\Model\InstructionsConfigProvider

{
    protected function getInstructions($code)
    {
        if($code == 'banktransfer'){ //check payment method is banktransfer
            return nl2br($this->methods[$code]->getInstructions());
        }else{
            return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
        }
    }

}