<?php

namespace Mtwo\Analytics\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Eav\Model\Config as EavConfig;

class Data extends AbstractHelper
{
	protected $orderCollectionFactory;
	protected $customerArray=[];
	public function __construct(
		\Magento\Framework\App\Helper\Context $context
		,OrderCollectionFactory $orderCollectionFactory
		,\Magento\Customer\Model\CustomerFactory $customerFactory
		,EavConfig $eavConfig

	) {
		$this->orderCollectionFactory = $orderCollectionFactory;
		$this->customerFactory = $customerFactory;
		$this->eavConfig = $eavConfig;

		parent::__construct($context);
	}

	public function getCustomerDetails($customerId){
		if(isset($this->customerArray[$customerId])){
		}else{
			$customer = $this->customerFactory->create()->load($customerId);
			if($customer->getId()){
				$this->customerArray[$customerId] = $customer;
			}
			else{
				return false;	
			}
		}
		return $this->customerArray[$customerId];

	}
	public function getAllInstitutes(){

		$attributes = array('institute'); // 'department',
		$result = array();
		foreach($attributes as $attributeCode){
			$attributeOption = $this->eavConfig->getAttribute('customer', $attributeCode);

			// Get all dropdown options
			$options = $attributeOption->getSource()->getAllOptions();
			// Prepare the result as an array of label-value pairs

			foreach ($options as $option) {
				if (!empty($option['value'])) {
					$result[$attributeCode][$option['value']] = $option['label'];
				}
			}
			// $result[$attributeCode][] = ['label' => 'Others', 'value' => 'others'];

		}
		return $result['institute'];

	}


	/**
     * Get the number of orders per month for the completed orders
     *
     * @return array
     */
	public function getOrdersAnalytics()
	{
		// Create the order collection
		$ordersCollection = $this->orderCollectionFactory->create();

		//	$ordersCollection->addFieldToFilter('status',array('neq'=>'canceled'));
		// Filter for completed orders and order by creation date
		$ordersCollection
			->setOrder('created_at', 'ASC');

		// Prepare the data array
		$ordersData = [];
		$ordersData['monthly'] = array();
		$ordersData['quarter'] = array();
		$ordersData['yearly'] = array();
		$total = 0;
		$ordersData['corporate'] = array(); // groupid 3,4,5,6 
		$ordersData['institute'] = array(); // groupid 1,2,7
		$ordersData['state'] = array();
		$ordersData['status'] = array();

		$institute_array = $this->getAllInstitutes();
		foreach ($ordersCollection as $order) {
			if($order->getCustomerId() == null || empty($order->getCustomerId()) || $order->getCustomerId() == 0){
				continue;	
			}

			$customer_group_id = $order->getCustomerGroupId();
			$customer_id = $order->getCustomerId();
			$customer = $this->getCustomerDetails($customer_id);
			if($customer === false){
				continue;	
			}

			$status = $order->getStatus();
			/** START : BY Status **/
			if(!isset($ordersData['status'][$status])){
				$ordersData['status'][$status]['order_count'] = 0;
				$ordersData['status'][$status]['name'] = ucFirst($status);
				$ordersData['status'][$status]['order_amount'] = 0;
			} 
			$ordersData['status'][$status]['order_count']++;
			$ordersData['status'][$status]['order_amount'] += $order->getGrandTotal();

			/** END : BY Status **/
			if($status == 'canceled'){
				continue;	
			}

			if($customer_group_id == '1' || $customer_group_id == '2' || $customer_group_id == '7'){
				$customer_group = 'institute';
				$institute_id =  $customer->getInstitute();
				if(isset($institute_array[$institute_id])){
					$institute_name = $institute_array[$institute_id];
				}else{
					continue;	
				}
				if(!isset($ordersData['institute'][$institute_id])){
					$ordersData['institute'][$institute_id]['order_count'] = 0;
					$ordersData['institute'][$institute_id]['name'] = $institute_name;
					$ordersData['institute'][$institute_id]['order_amount'] = 0;
				} 
				$ordersData['institute'][$institute_id]['order_count']++;
				$ordersData['institute'][$institute_id]['order_amount'] += $order->getGrandTotal();


			}
			else if($customer_group_id == '3' || $customer_group_id == '4' || $customer_group_id == '5' || $customer_group_id == '6'){
				$customer_group = 'corporate';

				if(!isset($ordersData['corporate'][$customer_id])){
					$ordersData['corporate'][$customer_id]['order_count'] = 0;
					$ordersData['corporate'][$customer_id]['name'] = $customer->getName();
					$ordersData['corporate'][$customer_id]['order_amount'] = 0;
				} 
				$ordersData['corporate'][$customer_id]['order_count']++;
				$ordersData['corporate'][$customer_id]['order_amount'] += $order->getGrandTotal();

			}else{
				continue;	
			}
			/** START : BY STATE **/
			$addresses = $customer->getAddresses();
			$region_name = $region_id = '';
			$shippingAddress = $order->getShippingAddress();

			if ($shippingAddress) {
				$region_id = $shippingAddress->getRegionId();  // Numeric region ID
				$region_name = $shippingAddress->getRegion();  // Name of the region
			}
			if($region_name != ''){
				if(!isset($ordersData['state'][$region_id])){
					$ordersData['state'][$region_id]['order_count'] = 0;
					$ordersData['state'][$region_id]['name'] = $region_name;
					$ordersData['state'][$region_id]['order_amount'] = 0;

				} 
				$ordersData['state'][$region_id]['order_count']++;
				$ordersData['state'][$region_id]['order_amount'] += $order->getGrandTotal();
			}
			/** END : BY STATE **/
			/** START : BY CUSTOMER **/
			if(!isset($ordersData['customer'][$customer_id])){
				$ordersData['customer'][$customer_id]['order_count'] = 0;
				$ordersData['customer'][$customer_id]['name'] = $customer->getName();
				$ordersData['customer'][$customer_id]['order_amount'] = 0;
			} 
			$ordersData['customer'][$customer_id]['order_count']++;
			$ordersData['customer'][$customer_id]['order_amount'] += $order->getGrandTotal();

			/** END : BY CUSTOMER **/




			// Format the month as "YYYY-MM"
			$createdAt = new \DateTime($order->getCreatedAt());

			$month = $createdAt->format('Y-M');
			$orderMonth = $createdAt->format('m');
			$yearly = $createdAt->format('Y');
			if($orderMonth == 4 || $orderMonth == 5 || $orderMonth == 6){
				$quarter = 'Apr-Jun';
			}
			else if($orderMonth == 7 || $orderMonth == 8 || $orderMonth == 9){
				$quarter = 'Jul-Sep';
			}else if($orderMonth == 10 || $orderMonth == 11 || $orderMonth == 12){
				$quarter = 'Oct-Dec';
			}else{
				$quarter = 'Jan-Mar';
			}
			$quarter = $yearly.'-'.$quarter;
			// Initialize the count for the month if not already set
			if (!isset($ordersData['monthly'][$month])) {
				$ordersData['monthly'][$month] = 0;
				$ordersData['monthly_amount'][$month] = 0;
			}
			if (!isset($ordersData['quarter'][$quarter])) {
				$ordersData['quarter'][$quarter] = 0;
				$ordersData['quarter_amount'][$quarter] = 0;
			}
			if (!isset($ordersData['yearly'][$yearly])) {
				$ordersData['yearly'][$yearly] = 0;
				$ordersData['yearly_amount'][$yearly] = 0;
			}
			$ordersData['yearly'][$yearly]++;
			$ordersData['quarter'][$quarter]++;
			$ordersData['monthly'][$month]++;

			$ordersData['yearly_amount'][$yearly] = $ordersData['yearly_amount'][$yearly] + $order->getGrandTotal();
			$ordersData['quarter_amount'][$quarter] = $ordersData['quarter_amount'][$quarter] + $order->getGrandTotal();
			$ordersData['monthly_amount'][$month] = $ordersData['monthly_amount'][$month] + $order->getGrandTotal();
			$total += $order->getGrandTotal(); 
		}
		$ordersData['total'] = $total;
		#echo $total;die;

		return $ordersData;
	}
	
	public function getMax($arr){
 		$max = max($arr);
		if($max < 100){
			$max = 	$max + (10 - ($max % 10)) + 20; 
		}else if($max < 1000){
			$max = 	$max + (100 - ($max % 100)) + 200; 
		}else {
			$max = 	$max + (1000 - ($max % 1000)) + 2000; 
		}
return $max;
	}

	public function getOrdersByAnalytics($customerId)
	{
		// Create the order collection

		$customer = $this->getCustomerDetails($customerId);

		$institute = $customer->getInstitute();
		$e_id[] = $customerId; 
		$customersData = $this->customerFactory->create()->getCollection()
			->addAttributeToFilter('institute',$institute);
			//->addAttributeToFilter('group_id', array('7','2')); 
			foreach($customersData as $customerD){
				$this->customerArray[$customerD->getId()] = $customerD;
				$e_id[] = $customerD->getId();
			}


		$ordersCollection = $this->orderCollectionFactory->create();

		$ordersCollection->addFieldToFilter('customer_id',array('in'=>$e_id));
		// Filter for completed orders and order by creation date
		$ordersCollection
			->setOrder('created_at', 'ASC');

		// Prepare the data array
		$ordersData = [];
		$ordersData['monthly'] = array();
		$ordersData['quarter'] = array();
		$ordersData['yearly'] = array();
		$total = 0;
		$ordersData['corporate'] = array(); // groupid 3,4,5,6 
		$ordersData['institute'] = array(); // groupid 1,2,7
		$ordersData['state'] = array();
		$ordersData['status'] = array();

		$institute_array = $this->getAllInstitutes();
		foreach ($ordersCollection as $order) {
			if($order->getCustomerId() == null || empty($order->getCustomerId()) || $order->getCustomerId() == 0){
				continue;	
			}

			$customer_group_id = $order->getCustomerGroupId();
			$customer_id = $order->getCustomerId();
			$customer = $this->getCustomerDetails($customer_id);
			if($customer === false){
				continue;	
			}

			$status = $order->getStatus();
			/** START : BY Status **/
			if(!isset($ordersData['status'][$status])){
				$ordersData['status'][$status]['order_count'] = 0;
				$ordersData['status'][$status]['name'] = ucFirst($status);
				$ordersData['status'][$status]['order_amount'] = 0;
			} 
			$ordersData['status'][$status]['order_count']++;
			$ordersData['status'][$status]['order_amount'] += $order->getGrandTotal();

			/** END : BY Status **/
			if($status == 'canceled'){
				continue;	
			}

			if($customer_group_id == '1' || $customer_group_id == '2' || $customer_group_id == '7'){
				$customer_group = 'institute';
				$institute_id =  $customer->getInstitute();
				if(isset($institute_array[$institute_id])){
					$institute_name = $institute_array[$institute_id];
				}else{
					continue;	
				}
				if(!isset($ordersData['institute'][$institute_id])){
					$ordersData['institute'][$institute_id]['order_count'] = 0;
					$ordersData['institute'][$institute_id]['name'] = $institute_name;
					$ordersData['institute'][$institute_id]['order_amount'] = 0;
				} 
				$ordersData['institute'][$institute_id]['order_count']++;
				$ordersData['institute'][$institute_id]['order_amount'] += $order->getGrandTotal();


			}
			else if($customer_group_id == '3' || $customer_group_id == '4' || $customer_group_id == '5' || $customer_group_id == '6'){
				$customer_group = 'corporate';

				if(!isset($ordersData['corporate'][$customer_id])){
					$ordersData['corporate'][$customer_id]['order_count'] = 0;
					$ordersData['corporate'][$customer_id]['name'] = $customer->getName();
					$ordersData['corporate'][$customer_id]['order_amount'] = 0;
				} 
				$ordersData['corporate'][$customer_id]['order_count']++;
				$ordersData['corporate'][$customer_id]['order_amount'] += $order->getGrandTotal();

			}else{
				continue;	
			}
			 
			/** START : BY CUSTOMER **/
			if(!isset($ordersData['customer'][$customer_id])){
				$ordersData['customer'][$customer_id]['order_count'] = 0;
				$ordersData['customer'][$customer_id]['name'] = $customer->getName();
				$ordersData['customer'][$customer_id]['order_amount'] = 0;
			} 
			$ordersData['customer'][$customer_id]['order_count']++;
			$ordersData['customer'][$customer_id]['order_amount'] += $order->getGrandTotal();

			/** END : BY CUSTOMER **/




			// Format the month as "YYYY-MM"
			$createdAt = new \DateTime($order->getCreatedAt());

			$month = $createdAt->format('Y-M');
			$orderMonth = $createdAt->format('m');
			$yearly = $createdAt->format('Y');
			if($orderMonth == 4 || $orderMonth == 5 || $orderMonth == 6){
				$quarter = 'Apr-Jun';
			}
			else if($orderMonth == 7 || $orderMonth == 8 || $orderMonth == 9){
				$quarter = 'Jul-Sep';
			}else if($orderMonth == 10 || $orderMonth == 11 || $orderMonth == 12){
				$quarter = 'Oct-Dec';
			}else{
				$quarter = 'Jan-Mar';
			}
			$quarter = $yearly.'-'.$quarter;
			// Initialize the count for the month if not already set
			if (!isset($ordersData['monthly'][$month])) {
				$ordersData['monthly'][$month] = 0;
				$ordersData['monthly_amount'][$month] = 0;
			}
			if (!isset($ordersData['quarter'][$quarter])) {
				$ordersData['quarter'][$quarter] = 0;
				$ordersData['quarter_amount'][$quarter] = 0;
			}
			if (!isset($ordersData['yearly'][$yearly])) {
				$ordersData['yearly'][$yearly] = 0;
				$ordersData['yearly_amount'][$yearly] = 0;
			}
			$ordersData['yearly'][$yearly]++;
			$ordersData['quarter'][$quarter]++;
			$ordersData['monthly'][$month]++;

			$ordersData['yearly_amount'][$yearly] = $ordersData['yearly_amount'][$yearly] + $order->getGrandTotal();
			$ordersData['quarter_amount'][$quarter] = $ordersData['quarter_amount'][$quarter] + $order->getGrandTotal();
			$ordersData['monthly_amount'][$month] = $ordersData['monthly_amount'][$month] + $order->getGrandTotal();
			$total += $order->getGrandTotal(); 
		}
		$ordersData['total'] = $total;
		
		
		foreach($ordersData['status'] as $status=>$data){
			$ordersData['status_data'][] = $data;
		}
		unset($ordersData['status']);
		foreach($ordersData['institute'] as $status=>$data){
			$ordersData['institute_data'][] = $data;
		}
		unset($ordersData['institute']);
	foreach($ordersData['corporate'] as $status=>$data){
			$ordersData['corporate_data'][] = $data;
		}
		unset($ordersData['corporate']);
foreach($ordersData['customer'] as $status=>$data){
			$ordersData['customer_data'][] = $data;
		}
		unset($ordersData['customer']);
		
		#echo $total;die;
		return $ordersData;
	}
}