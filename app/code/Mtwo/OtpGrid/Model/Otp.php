<?php
namespace Mtwo\OtpGrid\Model;

class Otp extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mtwo\OtpGrid\Model\ResourceModel\Otp');
    }
}
?>