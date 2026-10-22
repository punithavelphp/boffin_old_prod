<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_MobileLogin
 * @author  Sai Ram <sairam@egrovesys.com>
 */
declare(strict_types = 1);
namespace Emc\Otp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 *
 * @package Emc\MobileLogin\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Checks Whether valid Mobile Number or not
     *
     * @param string $userName
     * @return bool
     */
    public function checkIsMobileNumber(string $userName):bool
    {
        if (!(preg_match('/^[\d]{8,16}$/', $userName))
            && !(preg_match('/^[\d]{2,4}[-][\d]{2,4}[-][\d]{3,6}$/', $userName))
            && !(preg_match('/^[\d]{2,4}[.][\d]{2,4}[.][\d]{3,6}$/', $userName))
        ) {
            return false;
        }
        return true;
    }

    /**
     * Generate Random Strings
     *
     * @param array $characterTypes
     * @return string
     */
    public function generateRandomString(array $characterTypes):string
    {
        
        
        $randomString = "";
        foreach ($characterTypes as $characterType) {
            $characters = $characterType['characters'];
            $charactersLength = strlen($characters);
            for ($stringLength = 0; $stringLength < $characterType['length']; $stringLength++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        }
        return $randomString;
    }
    

}