<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Emc\Otp\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class EmcOtp
 *
 * @package Emc\Otp\Model
 */
class EmcOtpCount extends AbstractModel
{
    /**
     * Define Resource Model
     */
    public function _construct()
    {
        $this->_init(\Emc\Otp\Model\ResourceModel\EmcOtpCount::class);
    }
}