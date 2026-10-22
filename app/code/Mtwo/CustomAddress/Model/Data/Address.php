<?php

namespace Mtwo\CustomAddress\Model\Data;

/**
 * Description of Address
 *
 * @author Japhet Perez
 */
class Address extends \Magento\Customer\Model\Data\Address implements \Mtwo\CustomAddress\Api\Data\AddressInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}
