<?php

namespace Mtwo\ApproveOrder\Block\Index;

use Magento\Framework\View\Element\Template;

class Orders extends Template
{
	protected $orderFactory;
	protected $orderCollection;
	protected $customerSession;
	protected $facultyData ;
	protected $customerData;

	public function __construct(
		Template\Context $context,
		\Magento\Sales\Model\Order $orderFactory, // Add your custom Model
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection, // Add your custom Model
		\Magento\Customer\Model\Session $customerSession,
		\Mtwo\Approver\Model\Approvers $approvers,
		\Mtwo\Approver\Model\Approved $approved,
		\Mtwo\Approver\Model\EmployeeApprover $employeeApprover,


		\Magento\Customer\Model\CustomerFactory $customerFactory,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->orderFactory = $orderFactory;
		$this->orderCollection = $orderCollection;
		$this->customerSession = $customerSession;
		$this->approvers = $approvers;
		$this->approved = $approved;
		$this->customerFactory = $customerFactory;
		$this->employeeApprover = $employeeApprover;
		$this->facultyData = [];
		$this->customerData = [];

	}
	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__('Faculty/Student Orders'));
		parent::_prepareLayout();
		$page_size = $this->getPagerCount();
		$page_data = $this->getCustomData();
		if ($this->getCustomData()) {
			$pager = $this->getLayout()->createBlock(
				\Magento\Theme\Block\Html\Pager::class,
				'custom.pager.name'
			)
				->setAvailableLimit($page_size)
				->setShowPerPage(true)
				->setCollection($page_data);
			$this->setChild('pager', $pager);
			$this->getCustomData()->load();
		}
		return $this;
	}
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function getPageSize(){

		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		return $page;
	}

	public function getPageLimit(){

		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
		return $pageSize;
	}

	public function displayFacultyName($customer_id){
		$faculty_name = '';
		$facultyId = '';
		if(isset($this->customerData[$customer_id])) {
			$customersData = $this->customerData[$customer_id];
		}else{
			$customersData = $this->customerFactory->create()->load($customer_id);
			$this->customerData[$customer_id] = $customersData;
			$facultyId = $this->customerData[$customer_id]->getFaculty();
		}
		if(isset($this->customerData[$customer_id]) && $facultyId != ''){
			if(isset($this->customerData[$facultyId])){
				$FacultyData = $this->customerData[$facultyId];
				$faculty_name =  $FacultyData->getFirstname().' '.$FacultyData->getLastname();
			}else{
				$FacultyData = $this->customerFactory->create()->load($facultyId);
				$this->customerData[$facultyId] = $FacultyData;
				$faculty_name =  $FacultyData->getFirstname().' '.$FacultyData->getLastname();
			}
 		}
		return $faculty_name;
	}

	public function getCustomData()
	{
		// get param values
		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5; // set minimum records
		// get custom collection

		$customer_id = $this->customerSession->getCustomer()->getId();
		$customerGroupId = $this->getCurrentCustomerGroupId();		 

		// echo $this_owner_id;
		//$this->employeeApprover



		$institute = $this->customerSession->getCustomer()->getInstitute();
		$department = $this->customerSession->getCustomer()->getDepartment();
		$customersData = $this->customerFactory->create()->getCollection()
			->addAttributeToSelect('entity_id')
			->addAttributeToFilter('institute',$institute)
			->addAttributeToFilter('department',$department);
		$customersData->addAttributeToFilter('group_id',array(1,2)); 


		foreach($customersData as $customerD){
			$customers_entity[] = $customerD->getId();
		}
		if(isset($customers_entity)){
			
		}else{
			$customers_entity[] = -1;	
		}



		$collection = $this->orderFactory->getCollection();
		$collection->addFieldToFilter('customer_id',array('in'=>$customers_entity));
		$collection->addFieldToFilter('status',array('nin'=>array('canceled')));
		$collection->setPageSize($pageSize);
		$collection->setCurPage($page);
		$collection->setOrder('order_approval_status','ASC');
		$collection->setOrder('entity_id','Desc');


		// echo $collection->getSelect();die;

		return $collection;
	}

	public function getPagerCount()
	{
		// get collection
		$minimum_show = 5; // set minimum records
		$page_array = [];
		$list_data = $this->orderCollection->create();
		$list_count = ceil(count($list_data->getData()));
		$show_count = $minimum_show + 1;
		if (count($list_data->getData()) >= $show_count) {
			$list_count = $list_count / $minimum_show;
			$page_nu = $total = $minimum_show;
			$page_array[$minimum_show] = $minimum_show;
			for ($x = 0; $x <= $list_count; $x++) {
				$total = $total + $page_nu;
				$page_array[$total] = $total;
			}
		} else {
			$page_array[$minimum_show] = $minimum_show;
			$minimum_show = $minimum_show + $minimum_show;
			$page_array[$minimum_show] = $minimum_show;
		}
		return $page_array;
	}


	/* public function getApprovers(){
		$customer_id = $this->customerSession->getCustomer()->getId();
		//echo $customer_id;
 		$faculty_attr = 165;
		$subquery = new \Zend_Db_Expr("(SELECT DISTINCT `value` as fac_cus_id from customer_entity_int where `entity_id` = '".$customer_id."' and attribute_id = '".$faculty_attr."')");
		$approvers = $this->approvers->getCollection();
		$approvers->setOrder('priority','ASC');

		return $approvers->getData();
	} */


	public function getApprovers(){
		$customer_id = $this->customerSession->getCustomer()->getId();
		$owner_id = $this->customerSession->getCustomer()->getFaculty();
		$approvers = $this->employeeApprover
			->getCollection()
			->addFieldToFilter('manager_id',$customer_id)
			->setOrder('priority','ASC');
		return $approvers->getData();
	}

	public function getApproverByOrderCustomer($id){
		$owner_id = $this->customerSession->getCustomer()->getFaculty();
		$approvers = $this->employeeApprover
			->getCollection()
			->addFieldToFilter('employee_id',$id)
			->setOrder('priority','ASC');
		return $approvers->getData();
	}

	public function getApprovedBy($orderId,$approvers_id){
		$approvedData = array();
		$approved = $this->approved->getCollection();
		$approved->addFieldToFilter('order_id',$orderId);
		$approved->addFieldToFilter('manager_id',array('in'=>$approvers_id));
		$approved->setOrder('created_at','ASC');
		//  echo $approved->getSelect(); die;
		foreach($approved as $approved){
			$approvedData[] = $approved->getData('manager_id');
		}
		return $approvedData;
	} 
	
	



	public  function setSessionAllApprovers($approvers_id){
		$this->unsSessionAllApprovers();
		$this->customerSession->setAllApprovers($approvers_id);

	}

	public function getSessionAllApprovers(){
		$ses =  $this->customerSession->getAllApprovers();
		$this->unsSessionAllApprovers();
		return $ses;
	}

	public function unsSessionAllApprovers(){
		$this->customerSession->unsAllApprovers();

	}

	public  function setsessionApproveOrder($orderIdArray){
		$this->unsetsessionApproveOrder();
		$this->customerSession->setApproveOrder($orderIdArray);

	}

	public function getsessionApproveOrder(){
		$ses =  $this->customerSession->getApproveOrder();
		$this->unsetsessionApproveOrder();
		return $ses;

	}

	public function unsetsessionApproveOrder(){
		$this->customerSession->unsApproveOrder();

	}

	public function getCustomerById($id){
		if($id == 0)
			return  '';
		$cus =  $this->customerFactory->create()->load($id);
		if($cus->getId()){
			return ' ('.$cus->getFirstname().' - '.$cus->getId().')';	
		}
		return  '';
	}

	public function getCustomerId(){
		return $this->customerSession->getCustomer()->getId();
	}


	public function getCurrentCustomerGroupId(){
		return $this->customerSession->getCustomer()->getGroupId();
	}

	public function getIsApprover(){
		$gId = $this->customerSession->getCustomer()->getGroupId();

		if($gId == 3 ||   $gId == 2){
			return $gId;
		}
		return 0;

	}



}
