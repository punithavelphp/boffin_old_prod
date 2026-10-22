<?php

namespace Mtwo\Brandimage\Model\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class Sellers extends AbstractSource {
	protected $optionFactory;
	public function getAllOptions()
	{
		$this->_options = [];
		$this->_options[] = ['label' => 'Label 1', 'value' => '207'];
		$this->_options[] = ['label' => 'Label 2', 'value' => '208'];
		return $this->_options;
	}
}