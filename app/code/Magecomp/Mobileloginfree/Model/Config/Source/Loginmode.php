<?php
namespace Magecomp\Mobileloginfree\Model\Config\Source;

class Loginmode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Mobile Number and Password')],
            ['value' => 2, 'label' => __('Mobile Number/Email and Password')],
        ];
    }
}
