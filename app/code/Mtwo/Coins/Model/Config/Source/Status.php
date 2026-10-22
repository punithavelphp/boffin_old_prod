<?php
namespace Mtwo\Coins\Model\Config\Source;
use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                 'value' => $value,
                 'label' => $label,
             ];
        }

        return $result;
    }

    public function getOptions()
    {
        return [
 			'0' => __('Order Placed'),
			'1' => __('Approved'),
			'2' => __('Rejected'),
			'3' => __('In Transit'),
			'4' => __('Completed')
        ];
    }
}
	
	