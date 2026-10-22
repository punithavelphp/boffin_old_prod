<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Emc\Otp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class EmcOtp
 *
 * @package Emc\Otp\Model\ResourceModel
 */
class EmcOtpCount extends AbstractDb
{

    /**
     * Define Main table and Primary Id
     */
    public function _construct()
    {
        $this->_init('emc_otp_count', 'id');
    }
 
}