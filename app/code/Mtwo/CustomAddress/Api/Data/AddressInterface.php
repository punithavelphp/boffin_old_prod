<?php

namespace Mtwo\CustomAddress\Api\Data;

use Magento\Customer\Api\Data\AddressInterface as MainAddressInterface;

/**
 * Description of AddressInterface
 *
  */
interface AddressInterface extends MainAddressInterface
{
    const is_ACTIVE = 'is_active';
    
    /*
     * Get Address Active
     * @return string
     */
    public function getIsActive();
    
    /*
     * Set Address Active
     * @param string isActive
     * @return $this
     */
    public function setIsActive($isActive);
}
