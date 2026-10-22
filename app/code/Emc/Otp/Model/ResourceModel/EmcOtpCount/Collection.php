<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Emc\Otp\Model\ResourceModel\EmcOtpCount;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Emc\Otp\Model\ResourceModel\EmcOtpCount
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Define Resource and Model
     */
    public function _construct()
    {
        $this->_init(
            \Emc\Otp\Model\EmcOtpCount::class,
            \Emc\Otp\Model\ResourceModel\EmcOtpCount::class
        );
    }
}