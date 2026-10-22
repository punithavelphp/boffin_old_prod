<?php
namespace Mtwo\Refrens\Helper;

use Magento\Framework\App\Helper;

class Data extends Helper\AbstractHelper
{
 
    /**
     * Cache constructor.
     * @param Helper\Context $context
      */
    public function __construct(
        Helper\Context $context,
     ) {
          parent::__construct($context);
    }
 
}
