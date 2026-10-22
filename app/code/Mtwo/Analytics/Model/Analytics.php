<?php
namespace Mtwo\Analytics\Model;

use Mtwo\Analytics\Api\AnalyticsInterface;
use Mtwo\Analytics\Helper\Data;



class Analytics implements AnalyticsInterface {


	public function __construct( \Mtwo\Analytics\Helper\Data $helper

							   ) {
		$this->helper = $helper;

	}

	public function getAnalytics() { 
		return array($this->helper->getOrdersAnalytics());

	}  
	public function getCustomerAnalytics($customerId) { 
		return array($this->helper->getOrdersByAnalytics($customerId));

	}  
}