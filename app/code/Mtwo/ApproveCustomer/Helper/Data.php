<?php 
namespace Mtwo\ApproveCustomer\Helper;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\CustomerFactory;
use \Mageplaza\CustomerApproval\Helper\Data as MageplazaHelper;
use Mtwo\Approver\Model\ApproversFactory;
use Mtwo\Approver\Model\EmployeeApproverFactory;


class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	public $customerFactory;
	public $_addressFactory;
	public $_groupFactory;
	public $customerSession;
	public $mageplazaHelper;
	public $approverFactory;
	public $employeeApproverFactory;


	protected $_storeManager;
	const XML_PATH_APPROVER_COUNT = 'customer/custom/approval';
	protected $approverData = array();

	public function __construct(
		\Magento\Framework\App\Helper\Context $context
		,\Magento\Store\Model\StoreManagerInterface $storeManager 
		,\Magento\Customer\Model\Session $customerSession
		, CustomerFactory $customerFactory
		, \Magento\Customer\Model\AddressFactory $addressfactory
		, \Magento\Customer\Model\GroupFactory $groupFactory
		, MageplazaHelper $mageplazaHelper
		,ApproversFactory $approverFactory
		,EmployeeApproverFactory $employeeApproverFactory

		,\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->_storeManager = $storeManager;
		$this->customerFactory = $customerFactory;
		$this->_addressFactory = $addressfactory;
		$this->_groupFactory = $groupFactory;
		$this->customerSession = $customerSession;
		$this->mageplazaHelper = $mageplazaHelper;
		$this->approverFactory = $approverFactory;
		$this->customerSession = $customerSession;
		$this->scopeConfig = $scopeConfig;
		$this->employeeApproverFactory = $employeeApproverFactory;
		$this->approverData = [];
		parent::__construct($context);
	}
	public function addApproverDataForUser($employerId,$managerId,$employeeId,$priority){
		$count = 0;
		$approvers = $this->employeeApproverFactory->create()->getCollection()
			->addFieldToSelect('*')
			->addFieldToFilter('manager_id',$managerId)
			->addFieldToFilter('owner_id',$employerId)
			->addFieldToFilter('employee_id',$employeeId)
			;
		if($approvers->getSize()){

		}else{
			$this->employeeApproverFactory->create()->setOwnerId($employerId)->setManagerId($managerId)->setEmployeeId($employeeId)
				->setPriority($priority)->setCreatedAt(date('Y-m-d H:i:s'))
				->save();
			//$count = $count +1;
			// $this->updateApprover($count);
		}

	}
	public function addApproverData($employerId,$employeeId){
		$count = $this->getApproverCount();

		$approvers = $this->approverFactory->create()->getCollection()
			->addFieldToSelect('*')
			->addFieldToFilter('employee_id',$employeeId)
			->addFieldToFilter('employer_id',$employerId);
		if($approvers->getSize()){
		}else{
			$this->approverFactory->create()->setEmployerId($employerId)->setEmployeeId($employeeId)
				->setPriority($count+1)->setCreatedAt(date('Y-m-d H:i:s'))
				->save();
			//	$count = $count +1;
			//$this->updateApprover($count);
		}
	}
	public function updatePosition($index,$newPosition){
		$this->approverFactory->create()->load($index)->setPriority($newPosition)->save();
	}



	public function updateApproverPosition($index,$newPosition){
		$this->employeeApproverFactory->create()->load($index)->setPriority($newPosition)->save();
	}

	public function getCustomFormData($true) {
		return $this->customerSession->getCustomFormData(); 
	}


	public function getMaxApproverCount(){
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

		$count =  $this->scopeConfig->getValue(self::XML_PATH_APPROVER_COUNT, $storeScope);

		$count = (int)$count;
		if($count <= 0){
			return 2;	
		}
		return $count;
	}


	public function getApproverCount(){
		$count =  $this->customerSession->getCustomer()->getApproverCount();
		$count = (int)$count;
		return $count;
	}
	public function getIsLoggedIn(){
		return $this->customerSession->isLoggedIn();
	}
	public function getGroupId(){
		return $this->customerSession->getCustomer()->getGroupId();
	}


	public function getCustomerId(){
		return $this->customerSession->getCustomer()->getId();
	}





	public function getApproverCollection($cusId = ''){
		if($cusId == '') {
			$cusId = $this->customerSession->getCustomer()->getId();
		}
		$entity_array = array();
		$approverCollection =   $this->approverFactory->create()->getCollection()
			->addFieldToSelect("*")
			->addFieldToFilter("employer_id", array("eq" =>$cusId))
			->setOrder('priority','ASC');
		if($approverCollection->getSize()){
			foreach($approverCollection as $approvers){
				$entity_array[$approvers->getEmployeeId()] = 	$approvers->getData();
			}
		}
		return $entity_array;
	}

	public function getApproverDetails($entity_array){
		$customer_array = array();
		$customerCollection =   $this->customerFactory->create()->getCollection()
			->addAttributeToSelect("*")
			->addAttributeToFilter("entity_id", array("in" =>array_keys($entity_array)));
		if($customerCollection->getSize()){
			foreach($customerCollection as $c){
				$customer_array[$c->getId()] = $c->getData();
			}
		}
		return $customer_array;
	}


	public function updateApprover($count){
		$cusId = $this->customerSession->getCustomer()->getId();
		$customer = $this->customerFactory->create()->load($cusId);
		$customer->setApproverCount($count)->save();
	}

	public function updateEmployeeApproverCount($count,$customer){
		$customer->setApproverCount($count)->save();
	}


	public function getAccount($id){
		$customer =  $this->customerFactory->create()->load($id);
		if($customer->getId()){
			return $customer->getData();	
		}
		return false;
	}
	public function getCustomerDataById($id){
		if(isset($this->approverData[$id])) {
			return $this->approverData[$id];	
		}
		$customer =  $this->customerFactory->create()->load($id);
		if($customer->getId()){
			$this->approverData[$id] = $customer->getData();
			return $customer->getData();	
		}
		return false;
	}


	public function updateAccount($data){


		$errorUpdation['group_id'] = $data['group_id']; 
		$newStudent =  $this->customerFactory->create()->load($data['entity_id']);
		$cusId = $this->customerSession->getCustomer()->getId();

		if($newStudent->getId()){
			$customerExist =   $this->customerFactory->create()->getCollection()
				->addAttributeToSelect("*")
				->addAttributeToFilter("entity_id", array("neq" =>$newStudent->getId()))
				->addAttributeToFilter("email", array("eq" =>$data['email']));
			if($customerExist->getSize()){
				$errorUpdation['text']  = 'Email Already Exists';
				$errorUpdation['status'] = false; 
				return $errorUpdation;
			}
			$customerCollection =   $this->customerFactory->create()->getCollection()
				->addAttributeToSelect("*")
				->addAttributeToFilter("entity_id", array("neq" =>$newStudent->getId()))
				->addAttributeToFilter("mobilenumber", array("eq" =>$data['mobilenumber']));
			if($customerCollection->getSize()){
				$errorUpdation['text']  = 'Mobile Number Already Exists';
				$errorUpdation['status'] = false;
				return $errorUpdation;
			}
			try{

				$newStudent
					->setFirstname($data['firstname'])
					->setLastname($data['lastname'])
					->setMobilenumber($data['mobilenumber'])
					->setEmail($data['email']);

				if(isset($data['designation']) && $data['designation'] != ''){
					$newStudent->setDesignation($data['designation']);
				}
				if(isset($data['role_dept']) && $data['role_dept'] != ''){
					$newStudent->setRoleDept($data['role_dept']);
				}



				$newStudent->save() ;


				if($data['group_id'] == 5) {
					$this->addApproverData($cusId,$newStudent->getId());
				}

				if($data['group_id'] == 4 && isset($data['approver_id'])) {
					$countData = $this->employeeApproverCollection($newStudent->getId());
					$i=$countData->getSize();;
					foreach($data['approver_id'] as $key=> $manager){
						if($manager != ''){
							$i+1;
							$this->addApproverDataForUser($cusId,$manager,$newStudent->getId(),$i);


						}
					} 
					$countData = $this->employeeApproverCollection($newStudent->getId());
					if($size = $countData->getSize()) {
						$this->updateEmployeeApproverCount($size,$newStudent);
					}

				}


			}  catch (Exception $exception) {
				$errorUpdation['text']  =   $exception->getMessage();
				$errorUpdation['status'] = false;
				return $errorUpdation;
			}
		} else{

			$errorUpdation['text']  =   'User not exist';
			$errorUpdation['status'] = false;
			return $errorUpdation;

		}

		$errorUpdation['text']  = 'Updated Successfully';
		$errorUpdation['status'] = true; 
		$errorUpdation['edit'] = true; 
		return $errorUpdation;



	}

	public function createAccount($data){
		$errorUpdation['group_id'] = $data['group_id']; 
		if($data['email'] == ''){
			$errorUpdation['text']  = 'Email is Required';
			$errorUpdation['status'] = false; 
			return $errorUpdation;
		}

		if(isset($data['entity_id']) && $data['entity_id'] != '') {
			return $this->updateAccount($data);
		}
		$store = $this->_storeManager->getStore();
		$storeId = $store->getStoreId();
		$websiteId = $this->_storeManager->getStore()->getWebsiteId();
		$cusId = $this->customerSession->getCustomer()->getId();
		$customer = $this->customerFactory->create()->load($cusId);


		$customerExist = $this->customerFactory->create();
		$customerExist->setWebsiteId($websiteId);
		$customerExist->loadByEmail($data['email']);// load customer by email address

		if(!$customerExist->getId()){
			$customerCollection =   $this->customerFactory->create()->getCollection()
				->addAttributeToSelect("*")
				->addAttributeToFilter("mobilenumber", array("eq" =>$data['mobilenumber']));
			if($customerCollection->getSize()){
				$errorUpdation['text']  = 'Mobile Number Already Exists';
				$errorUpdation['status'] = false;
				return $errorUpdation;
			}else{

				try{

					$password = $this->randomPassword();
					$newStudent =  $this->customerFactory->create()->setWebsiteId($websiteId)
						->setStore($store)
						->setFirstname($data['firstname'])
						->setLastname($data['lastname'])
						->setMobilenumber($data['mobilenumber'])
						->setOtp('123456')
						->setIsApproved('approved')
						->setEmail($data['email'])
						->setPassword($password);


					if(isset($data['designation']) && $data['designation'] != ''){
						$newStudent->setDesignation($data['designation']);
					}
					if(isset($data['role_dept']) && $data['role_dept'] != ''){
						$newStudent->setRoleDept($data['role_dept']);
					}

					if($data['faculty_type'] == 2) {
						$newStudent->setFaculty($cusId);
						$newStudent->setDepartment($customer->getDepartment());
						$newStudent->setInstitute($customer->getInstitute());
						$newStudent->setUserType($data['group_id']);
						$newStudent->setGroupId($data['group_id']); 
					}elseif($data['faculty_type'] == 7) {
						$newStudent->setDepartment($customer->getDepartment());
						$newStudent->setInstitute($customer->getInstitute());
						$newStudent->setUserType($data['group_id']);
						$newStudent->setGroupId($data['group_id']); 
					}else{
						$newStudent->setFaculty($cusId);
						$newStudent->setUserType($data['group_id']);
						$newStudent->setGroupId($data['group_id']); // Approver
					}
					//$newStudent->setConfirmation(NULL);
					$newStudent->save();
					
					$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();

					$accountManagement = $objectManager->get('Magento\Customer\Api\AccountManagementInterface');
 					$accountManagement->resendConfirmation($newStudent->getEmail());


						if($data['group_id'] == 5) {
							$this->addApproverData($cusId,$newStudent->getId());
						}

					if($data['group_id'] == 4 && isset($data['approver_id'])) {
						$i=1;
						foreach($data['approver_id'] as $key=> $manager){
							if($manager != ''){
								$this->addApproverDataForUser($cusId,$manager,$newStudent->getId(),$i);
								$i+1;

							}
						}

						$countData = $this->employeeApproverCollection($newStudent->getId());


						if($size = $countData->getSize()) {
							$this->updateEmployeeApproverCount($size,$newStudent);
						}

					}

					$this->mageplazaHelper->emailAdminCreateAccount($newStudent,$password,'admin_success');


				}  catch (Exception $exception) {
					$errorUpdation['text']  =   $exception->getMessage();
					$errorUpdation['status'] = false;
					$errorUpdation['group_id'] = $data['group_id']; 
					return $errorUpdation;
				}	

				if($data['group_id'] == 6 ) { // || $data['group_id'] == 7

				}else{
					try {
						$newStudentId = $newStudent->getId(); // Pass dynamica Value
						$address = $this->_addressFactory->create();
						$address->setFirstname($data['firstname']);
						$address->setLastname($data['lastname']);
						$address->setTelephone($data['mobilenumber']);
						//$street[] = $data['street_1'];//pass  street as array
						//$street[] = $data['street_2'];
						$address->setStreet($customer->getDefaultShippingAddress()->getStreet());
						$address->setCity($customer->getDefaultShippingAddress()->getCity());
						$address->setCountryId('IN');
						$address->setPostcode($customer->getDefaultShippingAddress()->getPostcode());
						$address->setRegionId($customer->getDefaultShippingAddress()->getRegionId());
						$address->setCompany($customer->getDefaultShippingAddress()->getCompany());
						$address->setIsDefaultShipping(1);
						$address->setIsDefaultBilling(1);
						$address->setCustomerId($newStudentId);
						$address->save($address);
					} catch (Exception $exception) {
						$errorUpdation['text']  = 'Address Save issue';
						$errorUpdation['status'] = false;
						$errorUpdation['group_id'] = $data['group_id']; 
						return $errorUpdation;
					}
				}
			} 

		}else{
			$errorUpdation['text']  = 'Email Already Exists';
			$errorUpdation['status'] = false; 
			$errorUpdation['group_id'] = $data['group_id']; 
			return $errorUpdation;
		}

		//$this->setData('form_data', null);
		$errorUpdation['text']  = 'Added Successfully';
		$errorUpdation['status'] = true; 
		$errorUpdation['group_id'] = $data['group_id']; 
		return $errorUpdation;
	}
	
	
	public function createFacultyByStudent($student){
	 
		  
		$store = $this->_storeManager->getStore();
		$storeId = $store->getStoreId();
		$websiteId = $this->_storeManager->getStore()->getWebsiteId();
		$cusId = $student->getId();
		$customer = $this->customerFactory->create()->load($cusId);
		$data = $student = $customer->getData();

		$customerExist = $this->customerFactory->create();
		$customerExist->setWebsiteId($websiteId);
		if(!isset($student['faculty_email'])){
			$errorUpdation['text']  = 'Faculty is required';
				$errorUpdation['status'] = false;
				return $errorUpdation;
		}
		$customerExist->loadByEmail($student['faculty_email']);// load customer by email address

		if(!$customerExist->getId()){
			$customerCollection =   $this->customerFactory->create()->getCollection()
				->addAttributeToSelect("*")
				->addAttributeToFilter("mobilenumber", array("eq" =>$data['faculty_mobile']));
			if($customerCollection->getSize()){
				$errorUpdation['text']  = 'Mobile Number Already Exists';
				$errorUpdation['status'] = false;
				return $errorUpdation;
			}else{

				try{

					$password = $this->randomPassword();
					$newStudent =  $this->customerFactory->create()->setWebsiteId($websiteId)
						->setStore($store)
						->setFirstname($student['faculty_firstname'])
						->setLastname($student['faculty_lastname'])
						->setMobilenumber($student['faculty_mobile'])
						->setOtp('123456')
						 ->setIsApproved('pending')
						->setEmail($student['faculty_email'])
						->setPassword($password);


					if(isset($student['designation']) && $student['designation'] != ''){
						$newStudent->setDesignation($student['designation']);
					}
					if(isset($student['role_dept']) && $student['role_dept'] != ''){
						$newStudent->setRoleDept($student['role_dept']);
					}
 
						$newStudent->setDepartment($customer->getDepartment());
						$newStudent->setInstitute($customer->getInstitute());
						$newStudent->setUserType(3);
						$newStudent->setGroupId(2); 
					 
					$newStudent->setConfirmation(NULL);
					$newStudent->save();
					
					
					$errorUpdation['faculty_id']  = $newStudent->getId();
					 $errorUpdation['password']  = $password;
					
 

				}  catch (Exception $exception) {
					$errorUpdation['text']  =   $exception->getMessage();
					$errorUpdation['status'] = false;
					$errorUpdation['group_id'] = $data['group_id']; 
					return $errorUpdation;
				}	

				 
					try {
						$newStudentId = $newStudent->getId(); // Pass dynamica Value
						$address = $this->_addressFactory->create();
						$address->setFirstname($data['firstname']);
						$address->setLastname($data['lastname']);
						$address->setTelephone($data['mobilenumber']);
						//$street[] = $data['street_1'];//pass  street as array
						//$street[] = $data['street_2'];
						$address->setStreet($customer->getDefaultShippingAddress()->getStreet());
						$address->setCity($customer->getDefaultShippingAddress()->getCity());
						$address->setCountryId('IN');
						$address->setPostcode($customer->getDefaultShippingAddress()->getPostcode());
						$address->setRegionId($customer->getDefaultShippingAddress()->getRegionId());
						$address->setCompany($customer->getDefaultShippingAddress()->getCompany());
						$address->setIsDefaultShipping(1);
						$address->setIsDefaultBilling(1);
						$address->setCustomerId($newStudentId);
						$address->save($address);
					} catch (Exception $exception) {
						$errorUpdation['text']  = 'Address Save issue';
						$errorUpdation['status'] = false;
						$errorUpdation['group_id'] = $data['group_id']; 
						return $errorUpdation;
					}
				 
			} 

		}else{
			$errorUpdation['text']  = 'Email Already Exists';
			$errorUpdation['status'] = false; 
			$errorUpdation['group_id'] = $data['group_id']; 
			return $errorUpdation;
		}

		//$this->setData('form_data', null);
		
		$errorUpdation['text']  = 'Added Successfully';
		$errorUpdation['status'] = true; 
		$errorUpdation['group_id'] = $data['group_id']; 
		return $errorUpdation;
	}
	
	
	public function deleteApprover($id){
		$dats =  $this->employeeApproverFactory->create()
			->load( $id);
		$dats->delete();

	}


	public function employeeApproverCollection($customer_id = ''){
		if($customer_id == ''){
			$customer_id = $this->getCustomerId();
		}



		return	 $this->employeeApproverFactory->create()->getCollection()
			->addFieldToSelect('*')
			->addFieldToFilter('employee_id',$customer_id)
			->setOrder('priority','ASC')
			;

	}
	private function randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
	//	return 'admin@123';
		return implode($pass); //turn the array into a string
	}
}
?>
