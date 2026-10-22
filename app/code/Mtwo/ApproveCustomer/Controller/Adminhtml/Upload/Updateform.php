<?php

namespace Mtwo\ApproveCustomer\Controller\Adminhtml\Upload;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Session\SessionManagerInterface;
//use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerFactory;

#use Magento\Framework\App\ObjectManager;
use \Magento\Framework\Event\ManagerInterface;
use \Mageplaza\CustomerApproval\Helper\Data as MageplazaHelper;

class Updateform extends \Magento\Framework\App\Action\Action {

    protected $_fileUploaderFactory;
    protected $_directoryList;
    protected $_ioAdapter;
    protected $_csvProcessor;
    protected $_redirectFactory;
    protected $_url;
    protected $messageManager;
    protected $_storeManager;
    protected $customerFactory;
    protected $websiteId = 1;
    protected $_addressFactory;
    protected $_groupFactory;
    protected $_region;
    protected $_errorMessage = [];
    protected $_partCount;
    protected $eventManager;
    protected $_instituteArray = [];
    protected $_departmentArray = [];
	 protected $_cityArray=[];
	    protected $_logger;

    const COL_SEPERATOR = ',';

    public function __construct(
    Context $context, PageFactory $resultPageFactory
    , DirectoryList $directoryList
    , \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    , \Magento\Customer\Model\Session $customerSession
    , \Magento\Framework\Filesystem\Io\File $ioAdapter
    , \Magento\Framework\File\Csv $csvProcessor
    , \Magento\Framework\ObjectManagerInterface $objectManager
    , UrlInterface $url
    , RedirectFactory $redirectFactory
    , \Magento\Framework\Message\ManagerInterface $messageManager
    , SessionManagerInterface $coreSession
    , \Magento\Store\Model\StoreManagerInterface $storeManager
    , CustomerFactory $customerFactory
    , \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    , \Magento\Customer\Model\AddressFactory $addressfactory
    , \Magento\Customer\Model\GroupFactory $groupFactory
    , \Magento\Directory\Model\Region $region
    , ManagerInterface $eventManager
    , \Psr\Log\LoggerInterface $logger
		, \Eadesigndev\RomCity\Model\Cities $romcity
		,    \Magento\Eav\Model\Config $eavConfig
	,MageplazaHelper $mageplazaHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_directoryList = $directoryList;
        $this->customerSession = $customerSession;
        $this->_ioAdapter = $ioAdapter;
        $this->_csvProcessor = $csvProcessor;
        $this->_url = $url;
        $this->_redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->_addressFactory = $addressfactory;
        $this->_groupFactory = $groupFactory;
        $this->_region = $region;
        $this->_logger = $logger;
		$this->romcity = $romcity;
    	$this->eavConfig = $eavConfig;
		$this->mageplazaHelper = $mageplazaHelper;

         parent::__construct($context);
		$this->getDepartment();
		$this->getInstitute();
		
		 
    }

    public function execute() {
		 /*  $websiteId = $this->_storeManager->getStore()->getWebsiteId();
		 $customer = $this->customerFactory->create();
		 $customer->setWebsiteId($websiteId);
		 $customer->loadByEmail('punithavelkt+xg@gmail.com');
 		$ret = $this->mageplazaHelper->emailAdminCreateAccount($customer,'test@test','admin_success');
		if($ret){
		echo 'SENT';	
		}else{
			 
 		echo 'FALSE';		
		}
die;   */
		 
		$this->_cityArray = $this->romcity->getCitiesData();
		
        $rem_URL = $this->_storeManager->getStore()->getBaseUrl();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->isPost()) {
            $target = $this->getUploadPath();
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'update_customercsv_file']);
            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowRenameFiles(true);
            $save_file_name = date('Y-m-d-H-i-s') . '.csv';
            $result = $uploader->save($target, $save_file_name);
            $uploaded_file_name = $uploader->getUploadedFileName();
            $this->readCSVFile($uploaded_file_name);
        }
         $resultRedirect = $this->_redirectFactory->create();
        $resultRedirect->setUrl($this->_url->getUrl('customer/upload/index'));
       return $resultRedirect;
    }

    public function readCSVFile($file_name) {
        $target_path = $this->getUploadPath();
        $csvFile = $target_path . $file_name;
        $importCustomers = $this->_csvProcessor->getData($csvFile);
		
		
		
        $all_count = count($importCustomers);
        $error = array();
        $updateCustomerArray = [];
		$file_path = $this->_directoryList->getPath(DirectoryList::MEDIA);
        $folderPath = $file_path . "/customerupload/";
        $filename =  'customers_' . date('YmdHis') ;
		$pathToGenerate = $folderPath . "/" . $filename.'.csv';
		$createFile = fopen($pathToGenerate, "w+");
		
        foreach ($importCustomers as $index => $importCustomer) :
            $str = implode(',', $importCustomer);
            $str = $this->clean($str);
            if ($str == '') {
                $all_count = $all_count - 1;
                continue;
            }

            if ($index == 0):
                $header_array = $importCustomer;
				array_push($importCustomer, 'Error');
				fputcsv($createFile, $importCustomer);
                continue;
            endif;
			$errorResponse = $this->updateCustomerData($importCustomer, $header_array, $index);
			$data_array = $importCustomer;
			$errorResponseData = $errorResponse[$index];
		
			array_push($data_array, $errorResponseData);
		 
			fputcsv($createFile, $data_array);
            $error[] = $errorResponse;
        endforeach;
         fclose($createFile);
         
		
        $errorMessage = '';
        foreach ($error as $key => $valueErr) {
            if (is_array($valueErr)) {
                foreach ($valueErr as $vkey => $value) {
                    $message = $value;
                    $errorMessage .= 'Error in Row ' . ($key + 1) . ' - ' . $message . "\r\n";
                }
            }
        }
        if (count($error) > 0) {
            $error_count = count($error);
            if ($error_count < $this->_partCount) {
                $success_count = $this->_partCount - $error_count;
				if($success_count <= 0){
					                $this->messageManager->addError(__($errorMessage));

				}
else{
                $this->messageManager->addSuccess(__($success_count . ' / ' . $this->_partCount . ' updated successfully'));
}
            }
            if ($errorMessage) {

                $this->messageManager->addError(__($errorMessage));
				 $this->messageManager->addError('<a href="'.$this->_url->getUrl('customer/upload/download/file/'.$filename).'" target="_blank">Download Error File</a>');
            }
        } else {
            $this->messageManager->addSuccess(__($this->_partCount . ' of ' . ($all_count - 1) . ' Data updated Successfully'));
        }
    }

    protected function clean($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]-/', '', $string); // Removes special chars.

        return preg_replace('/[^A-Za-z0-9\]/', '', $string); // Removes special chars.
    }

    protected function getUploadPath() {
        $file_path = $this->_directoryList->getPath(DirectoryList::MEDIA);
        $retailer_path = $file_path . "/customerupload/";
        if (!is_dir($retailer_path))
            $this->_ioAdapter->mkdir($retailer_path, 0775);
        return $retailer_path;
    }
	
	  public function getInstitute(){
		   $attribute = $this->eavConfig->getAttribute('customer', 'institute');
			$options = $attribute->getSource()->getAllOptions();
		 foreach($options as $option){
				$label = strtolower($option['label']);
				if($option == '' || $option == 'others'){
				}else {
					$this->_instituteArray[$label] =$option['value'];
				}
			}
	   }

 		public function getDepartment(){
		    $attribute = $this->eavConfig->getAttribute('customer', 'department');
			$options = $attribute->getSource()->getAllOptions();
		 	foreach($options as $option){
				$label = strtolower($option['label']);
				if($option == '' || $option == 'others'){
				}else {
					$this->_departmentArray[$label] =$option['value'];
				}
			}
	   }
    protected function updateCustomerData($data_array, $header, $index) {
 
		  foreach ($header as $key => $column) {
            $column = $this->clean($column);
            $column = trim($column);
            $data[trim($column)] = $data_array[$key];
            //echo '$' . "customerArray['" . $column . "']" . '=<br>';
        }
		
         $errorUpdation = array(); 
		 $store = $this->_storeManager->getStore();
		 $storeId = $store->getStoreId();
		 $websiteId = $this->_storeManager->getStore()->getWebsiteId();
		 $customer = $this->customerFactory->create();
		 $customer->setWebsiteId($websiteId);
		 $customer->loadByEmail($data['email']);// load customer by email address
		 if(!$customer->getId()){
			 
			$customerCollection =   $this->customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("mobilenumber", array("eq" =>$data['mobilenumber']))
				;
 			if($customerCollection->getSize()){
 				$errorUpdation[$index] = 'Mobile Number Already Exists';
				return $errorUpdation;
			}
			 $department = strtolower($data['department']);
 
			 if(isset($this->_departmentArray[($department)])){
				 $departmentId = $this->_departmentArray[($department)];
			 }else{
				 $errorUpdation[$index] = 'Department is Invalid';
				return $errorUpdation;
			 }
			 $institute = strtolower($data['institute']);
			 if(isset($this->_instituteArray[($institute)])){
				$instituteId = $this->_instituteArray[($institute)];
			 }else{
				 $errorUpdation[$index] = 'Institute is Invalid';
				return $errorUpdation;
			 }
			 
			 $regionData = $this->_region->loadByName($data['state'], 'IN');

			 if ($regionData->getId()) {
				 $data['region_id'] = $regionData->getId();

				 $city = $this->_cityArray;	
				 if(isset($city[$data['region_id']]) && isset($city[$data['region_id']][strtolower($data['city'])])){
					$data['city'] = $city[$data['region_id']][strtolower($data['city'])];
				 }else{
					 $errorUpdation[$index] = 'Invalid City';
					 return $errorUpdation;
				 }

			 } else {
				 $updateAddress = false;
				 $errorUpdation[$index] = 'Invalid Region';
				 return $errorUpdation;
			 }
			  
			 try{
				 
				 $password = $this->randomPassword();
					 
					 
					 
				 	 $customer->setWebsiteId($websiteId)
			 ->setStore($store)
			 ->setFirstname($data['firstname'])
			 ->setLastname($data['lastname'])
			->setMobilenumber($data['mobilenumber'])
			->setOtp('123456')
			->setIsApproved('approved')
 			 ->setEmail($data['email'])
			 ->setPassword($password);
			 
				 $customer->setDepartment($departmentId);
				 $customer->setInstitute($instituteId);
				 $customer->setUserType(1);
				  $customer->setGroupId(2);
				  $customer->save();
				 
 				 
					 $this->mageplazaHelper->emailAdminCreateAccount($customer,$password,'admin_success');
					 
				 /*
				 
				 
        $this->_sendEmailTemplate(
            $types[$type],
            self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            ['customer' => $this, 'back_url' => $backUrl, 'store' => $this->getStore()],
            $storeId
        );
				 
				 */
				 
			 }  catch (Exception $exception) {
				 	$errorUpdation[$index] = 'Error in customer Data';
				return $errorUpdation;
			 }			 
			try {
				$customerId = $customer->getId(); // Pass dynamica Value
				$address = $this->_addressFactory->create();
				$address->setFirstname($data['firstname']);
				$address->setLastname($data['lastname']);
				$address->setTelephone($data['mobilenumber']);
				$street[] = $data['street_1'];//pass street as array
				$street[] = $data['street_2'];
				$address->setStreet($street);
				$address->setCity($data['city']);
				$address->setCountryId('IN');
				$address->setPostcode($data['postcode']);
				$address->setRegionId($data['region_id']);
				$address->setIsDefaultShipping(1);
				$address->setIsDefaultBilling(1);
				$address->setCustomerId($customerId);
				$address->save($address);
			} catch (Exception $exception) {
				 $errorUpdation[$index] = 'Address Issue';
                 return $errorUpdation;
			}
 		}else{
				$errorUpdation[$index] = 'Email Exists';
				
		}
		return $errorUpdation;
    }
	
	private function randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}


}
