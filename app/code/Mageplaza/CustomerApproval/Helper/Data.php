<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_CustomerApproval
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerApproval\Helper;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\CustomerApproval\Model\Config\Source\AttributeOptions;
use Mageplaza\CustomerApproval\Model\Config\Source\TypeAction;

/**
 * Class Data
 *
 * @package Mageplaza\CustomerApproval\Helper
 */
class Data extends AbstractData
{
	const CONFIG_MODULE_PATH = 'mpcustomerapproval';
	const XML_PATH_EMAIL     = 'email';

	/**
     * @var HttpContext
     */
	protected $_httpContext;

	/**
     * @var TransportBuilder
     */
	protected $transportBuilder;

	/**
     * @var CookieMetadataFactory
     */
	private $cookieMetadataFactory;

	/**
     * @var PhpCookieManager
     */
	private $cookieMetadataManager;

	/**
     * @var AttributeOptions
     */
	private $attributeOptions;

	/**
     * @var CustomerViewHelper
     */
	protected $customerViewHelper;

	/**
     * @var CustomerRegistry
     */
	protected $customerRegistry;

	protected $addressFactory;

	/**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param TransportBuilder $transportBuilder
     * @param AttributeOptions $attributeOptions
     * @param CustomerViewHelper $customerViewHelper
     * @param CustomerRegistry $customerRegistry
     */
	public function __construct(
		Context $context,
		ObjectManagerInterface $objectManager,
		StoreManagerInterface $storeManager,
		HttpContext $httpContext,
		TransportBuilder $transportBuilder,
		AttributeOptions $attributeOptions,
		CustomerViewHelper $customerViewHelper,
		CustomerRegistry $customerRegistry,
		\Magento\Customer\Model\AddressFactory $addressFactory,
		\Mtwo\CustomAddress\Model\Customaddress $Customaddress

	) {
		$this->_httpContext       = $httpContext;
		$this->transportBuilder   = $transportBuilder;
		$this->attributeOptions   = $attributeOptions;
		$this->customerViewHelper = $customerViewHelper;
		$this->customerRegistry   = $customerRegistry;
		$this->_addressFactory = $addressFactory;
		$this->customaddress = $Customaddress;
		$this->_storeManager = $storeManager;
		parent::__construct($context, $objectManager, $storeManager);
	}

	/**
     * @param $customerId
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
	public function getCustomerById($customerId)
	{
		$customerModel = $this->customerRegistry->retrieve($customerId);

		return $customerModel->getDataModel();
	}
	/**
     * @param int|null $customerId
     *
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
	public function getIsApproved($customerId)
	{
		$isApprovedObject = $this->getCustomerById($customerId)
			->getCustomAttribute('is_approved');
		if (!$isApprovedObject || !$isApprovedObject->getValue()) {
			$this->approvalAction($customerId, AttributeOptions::APPROVED);

			return AttributeOptions::APPROVED;
		}

		return $isApprovedObject->getValue();
	}

	/**
     * @param $status
     *
     * @return string
     */
	public function getApprovalLabel($status)
	{
		$options = $this->attributeOptions->toArray();
		if (!array_key_exists($status, $options)) {
			return '';
		}

		return $options[$status];
	}

	/**
     * @param $customerId
     * @param $typeAction
     *
     * @throws Exception
     */
	public function approvalCustomerById($customerId, $typeAction = TypeAction::OTHER)
	{
		$customer = $this->customerRegistry->retrieve($customerId);
		$groupId = $customer->getGroupId();

		$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();

		if($groupId == 2 || $groupId == 7){
			$institute =  $customer->getResource()->getAttribute('institute')->getFrontend()->getValue($customer);

			if(strtolower($institute) == 'others' || empty($institute)){

				$newInstitute = $customer->getData('institute_others');
				$this->addSingleOptionToCustomerAttribute('institute', $newInstitute);
				$instituteID = $this->getOptionIdByValue('institute', $newInstitute);
				$customerData = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->setWebsiteId(1)->load($customerId);
				if ($customerData) {
					$customer->setData('institute', $instituteID);
					$customer->save();
				}
				# throw new NoSuchEntityException(__('Institute  Not exist.'));
			}
			$department =  $customer->getResource()->getAttribute('department')->getFrontend()->getValue($customer);

			if(strtolower($department) == 'others' || empty($department)){
				$newDepartment = $customer->getData('department_others');
				$this->addSingleOptionToCustomerAttribute('department', $newDepartment);
				$departmentID = $this->getOptionIdByValue('department', $newDepartment);
				$customerData = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->setWebsiteId(1)->load($customerId);
				if ($customerData) {
					$customer->setData('department', $departmentID);
					$customer->save();
				}



				#throw new NoSuchEntityException(__(' Department Not exist.'));
			}
		}
		if($groupId == 1 || $groupId == 2 || $groupId == 7){
			if(empty($customer->getData('gstin_number')))
				$this->assignGST($customer);
			
		}

		$this->approvalAction($customer, AttributeOptions::APPROVED);
		// send email
		if ((!$this->getAutoApproveConfig() && !$this->isAdmin()) || $typeAction != TypeAction::OTHER) {
			$this->emailApprovalAction($customer, 'approve');
		}
	}


	protected function getSetup()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		return          ObjectManager::getInstance()->get(\Magento\Framework\Setup\ModuleDataSetupInterface::class);
	}

	/**
 * Add a single option to an existing customer attribute
 *
 * @param string $attributeCode
 * @param string $optionValue
 * @return int|null
 */
	public function addSingleOptionToCustomerAttribute($attributeCode, $optionValue)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$eavSetupFactory = $objectManager->get(\Magento\Eav\Setup\EavSetupFactory::class);



		$setup = $eavSetupFactory->create(['setup' => $this->getSetup()]);
		#$setup->startSetup();
		// Get the attribute ID
		$attributeId = $setup->getAttributeId(\Magento\Customer\Model\Customer::ENTITY, $attributeCode);

		if ($attributeId) {
			// Add the option
			$setup->addAttributeOption([
				'attribute_id' => $attributeId,
				'values' => [$optionValue],
			]);

			// Retrieve the newly added option ID
			$optionId = $this->getOptionIdByValue($attributeCode, $optionValue);

			#	$setup->endSetup();
			return $optionId; // Return the ID of the added option
		}

		# $setup->endSetup();
		return null; // Return null if the attribute ID doesn't exist
	}

	/**
 * Get the option ID by the option value
 *
 * @param string $attributeCode
 * @param string $optionValue
 * @return int|null
 */
	public function getOptionIdByValue($attributeCode, $optionValue)
	{

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$eavConfig = $objectManager->get('\Magento\Eav\Model\Config');




		$attributeOption = $eavConfig->getAttribute('customer', $attributeCode);

		// Get all dropdown options
		$options = $attributeOption->getSource()->getAllOptions();

		foreach ($options as $option) {
			if (strtolower($optionValue) == strtolower($option['label'])) {
				return  $option['value'];
			}
		}

		return null; // Return null if not found
	}





	/**
     * @param int $customerId
     *
     * @throws Exception
     */
	public function notApprovalCustomerById($customerId)
	{
		$customer = $this->customerRegistry->retrieve($customerId);
		$this->approvalAction($customer, AttributeOptions::NOTAPPROVE);
		// send email
		$this->emailApprovalAction($customer, 'not_approved');
	}

	public function updateFacultyDetailsToStudents(){
		$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();


		$faculyList =   $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->getCollection()
			->addAttributeToSelect('faculty_email')
			->addFieldToFilter('group_id',1)
			->addAttributeToFilter('faculty_email',array('notnull'=>true))
			->addAttributeToFilter('faculty',[
				['attribute' => 'value', 'null' => true], // Check if 'value' is NULL
				['attribute' => 'value', 'eq' => '0']     // Check if 'value' is empty
			],'left');

		foreach($faculyList as $studentFaculy){
			$faculy = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->setWebsiteId(1)->loadByEmail($studentFaculy->getData('faculty_email'));
			if($faculy->getId()){
				if($faculy->getId() == $studentFaculy->getId()){
					$msg = 'INCORRECT DATA--->Student Id : '.$studentFaculy->getId().' Student Email-->'.$studentFaculy->getEmail().' --  faculty EMAIL= '.$studentFaculy->getData('faculty_email');


					//	$studentFaculy->setData('faculty_email','')->setWebsiteId(1)->save();

					//	$student = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->setWebsiteId(1)->load($studentFaculy->getId())->setData('faculty_email','')->save();







					// send email
					$msg .= "<br>";

					$msg .= __FILE__;
					//	mail("punithavelkt@gmail.com","Faculty - Student Not Exist",$msg);
				}else{
					//	$studentFaculy->setData('faculty',$faculy->getId())->save();



					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
					$tableName = $resource->getTableName('customer_entity_int'); //gives table name with prefix



					//Insert Data into table
					$sql = "Insert Ignore Into " . $tableName . " ( `attribute_id`, `entity_id`, `value`) Values ('165','".$studentFaculy->getId()."','".$faculy->getId()."')";


					$connection->query($sql);




					$msg = 'SUCCESS--->Student Id : '.$studentFaculy->getId().' -- ASSIGNED faculty= '.$studentFaculy->getData('faculty_email');
					// send email
					$msg .= "<br>";
					$msg .= __FILE__;
					// 	mail("punithavelkt@gmail.com","Faculty - Student Not Exist",$msg);
				}

			}else{
				$msg = 'ERROR--->Student Id : '.$studentFaculy->getId().' -- Not able to assign faculty= '.$studentFaculy->getData('faculty_email');
				// send email
				$msg .= "<br>";
				$msg .= __FILE__;
				//	mail("punithavelkt@gmail.com","Faculty - Student Not Exist",$msg);


			}
		}


	}

	/**
     * @param Customer|int $customer
     * @param string $typeApproval
     *
     * @throws Exception
     */
	public function approvalAction($customer, $typeApproval)
	{

		$this->updateFacultyDetailsToStudents();
		if (is_int($customer)) {
			$customer = $this->customerRegistry->retrieve($customer);
		}

		if (!$customer instanceof Customer) {
			throw new NoSuchEntityException(__('Customer does not exist.'));
		}

		$customerData = $customer->getDataModel();
		$attribute    = $customerData->getCustomAttribute('is_approved');

		if ($attribute && $attribute->getValue() != $typeApproval) {
			$customerData->setId($customer->getId());
			$customerData->setCustomAttribute('is_approved', $typeApproval);
			$customer->updateData($customerData);
			$customer->save();
			$shippingAddressId 	= $customer->getDefaultShipping();
			$shippingAddress 	= $this->_addressFactory->create()->load($shippingAddressId);

			$shippingAddress = $this->customaddress->load($shippingAddressId);
			$shippingAddress->setIsActive(1)->save();
		}
	}

	/**
     * @param int $customerId
     * @param bool $actionRegister
     *
     * @throws Exception
     */
	public function setApprovePendingById($customerId, $actionRegister)
	{

		$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();
		$logger = $objectManager->get(\Psr\Log\LoggerInterface::class);

		// Log a message
		$logger->info('Your log message here.');
		$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog('-->setApprovePendingById-->');



		if ($this->getIsApproved($customerId) != AttributeOptions::PENDING) {
			$customer     = $this->customerRegistry->retrieve($customerId);
			$customerData = $customer->getDataModel();
			$customerData->setId($customerId);
			$websiteId = $this->_storeManager->getStore()->getWebsiteId();
			//	echo $customerData->getCustomAttribute('user_type')->getValue();
			//	echo '<br>';

			// 	$customerData->getCustomAttribute('user_type')->getValue()
			$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();
			$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->user_type-->'.$customerData->getCustomAttribute('user_type')->getValue());

			if($customerData->getCustomAttribute('user_type')->getValue() == 5){

				$student =   $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->load($customerId);
				// check and update faculty. 	faculty_email
				$faculty_email = $student->getFacultyEmail();

				$pos = strpos($faculty_email, 'FACULTYEXIST');
				if($pos === false){


					$customerExist = $objectManager->get('Magento\Customer\Model\CustomerFactory');

					$facultyData = $customerExist->create()->setWebsiteId($websiteId)->loadByEmail($faculty_email); // load customer by email address

					$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->faculty ID-->'.$facultyData->getId());

					if($facultyData->getId()){
						$student->setFaculty($facultyData->getId())->save();
						// send mail to faculty 

						$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->START EXIST send mail to faculty');
						$objectManager->get('Mtwo\ApproveOrder\Helper\Email')->sendFacultyApproveStudentMail($student,$facultyData,0,'');
						$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->End EXIST send mail to faculty');
					}else{
						$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->START Create faculty');
						$approveCustomerHelper = $objectManager->get('Mtwo\ApproveCustomer\Helper\Data')->createFacultyByStudent($student);
						if(isset($approveCustomerHelper['faculty_id']) && $approveCustomerHelper['password']){
							$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->password');

							$facultyData = $customerExist->create()->setWebsiteId($websiteId)->loadByEmail($faculty_email);
							if($facultyData->getId()){ 
								$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->New Faculty ID-->'.$facultyData->getId());

								$student
									// ->setFaculty($facultyData->getId())
									->save();


								$studentDataModel = $student->getDataModel();
								$studentDataModel->setWebsiteId($websiteId);
								$studentDataModel->setCustomAttribute('faculty',$facultyData->getId());
								$student->updateData($studentDataModel);
								$student->save();





								$created = 1;
								$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->Start NEW send mail to faculty');

								$objectManager->get('Mtwo\ApproveOrder\Helper\Email')->sendFacultyApproveStudentMail($student,$facultyData,$created,$approveCustomerHelper['password']);
								$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog(__LINE__.'-->End NEW send mail to faculty');

							}

						}
						// 
					}
				}
			}

			$groupId = $this->getGroupIdByUserType($customerData->getCustomAttribute('user_type')->getValue());
			$customerData->setGroupId($groupId);
			$customerData->setCustomAttribute('is_approved', AttributeOptions::PENDING);
			$customer->updateData($customerData);
			$customer->save();

			if ($actionRegister) {
				$this->emailApprovalAction($customer, 'success');
			}
		}
	}
	
	protected function assignGST($customer){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$instituteGstHelper = $objectManager->get('Mtwo\InstituteGst\Helper\InstituteGst');
		$instituteId = $customer->getData('institute');
		$gstNumber = $instituteGstHelper->getGstNumberById($instituteId);
		$customer->setData('gstin_number', $gstNumber); 
	
	}

	public function getGroupIdByUserType($userType){
		if($userType == 0){
			return 2;	
		}
		else if($userType == 1){
			return 7;	
		}
		else if($userType == 2){
			return 3;	
		}
		else if($userType == 3){
			return 2;	
		}else if($userType == 4){
			return 8;	
		}
		else if($userType == 5){
			return 1;	// student 

		}
		return 9;	
	}

	/**
     * @return bool
     */
	public function isCustomerApprovalEnabled()
	{
		return $this->isEnabled();
	}

	/**
     * @return mixed|null
     */
	public function getCustomerGroupId()
	{
		return $this->_httpContext->getValue(CustomerContext::CONTEXT_GROUP);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getEnabledNoticeAdmin($storeId = null)
	{
		return $this->getModuleConfig('admin_notification_email/enabled', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getNoticeAdminTemplate($storeId = null)
	{
		return $this->getModuleConfig('admin_notification_email/template', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getSenderAdmin($storeId = null)
	{
		return $this->getModuleConfig('admin_notification_email/sender', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getRecipientsAdmin($storeId = null)
	{
		return preg_replace('/\s+/', '', $this->getModuleConfig('admin_notification_email/sendto', $storeId));
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getSenderCustomer($storeId = null)
	{
		return $this->getModuleConfig('customer_notification_email/sender', $storeId);
	}

	/**
     * @param $type
     * @param null $storeId
     *
     * @return mixed
     */
	public function getEmailEnable($type, $storeId = null)
	{
		return $this->getModuleConfig(
			'customer_notification_email/customer_' . $type . '_email/enabled',
			$storeId
		);
	}

	/**
     * @param $type
     * @param null $storeId
     *
     * @return mixed
     */
	public function getEmailTemplate($type, $storeId = null)
	{
		return $this->getModuleConfig(
			'customer_notification_email/customer_' . $type . '_email/template',
			$storeId
		);
	}

	/**
     * @param $customer
     * @param $emailType
     */
	public function emailApprovalAction($customer, $emailType)
	{
		$storeId = $customer->getStoreId();
		$sendTo  = $customer->getEmail();
		$sender  = $this->getSenderCustomer($storeId);
		if ($this->getAutoApproveConfig()) {
			$sender = $this->getConfigValue('customer/create_account/email_identity', $storeId);
		}

		if ($this->getEmailEnable($emailType)) {
			$template = $this->getEmailTemplate($emailType, $storeId);
			$this->sendMail($sendTo, $customer, $template, $storeId, $sender);
		}
	}

	public function emailAdminCreateAccount($customer, $password, $emailType)
	{
		$storeId = $customer->getStoreId();
		$sendTo  = $customer->getEmail();
		$sender  = $this->getSenderCustomer($storeId);

		//  if ($this->getAutoApproveConfig()) {
		$sender = $this->getConfigValue('customer/create_account/email_identity', $storeId);
		//  }


		if ($this->getEmailEnable($emailType)) {

			$template = $this->getEmailTemplate($emailType, $storeId);

			return $this->sendMail($sendTo, $customer, $template, $storeId, $sender,$password);
		}

		return false;
	}

	/**
     * @param $customer
     */
	public function emailNotifyAdmin($customer)
	{
		$storeId = $customer->getStoreId();
		$sender  = $this->getSenderAdmin($storeId);
		if ($this->getAutoApproveConfig()) {
			$sender = $this->getConfigValue('customer/create_account/email_identity', $storeId);
		}
		$sendTo      = $this->getRecipientsAdmin();
		$sendToArray = explode(',', $sendTo);

		if ($this->getEnabledNoticeAdmin()) {
			// send email notify to admin
			foreach ($sendToArray as $recipient) {
				$this->sendMail(
					$recipient,
					$customer,
					$this->getNoticeAdminTemplate(),
					$storeId,
					$sender
				);
			}
		}
	}

	/**
     * @param $sendTo
     * @param $customer
     * @param $emailTemplate
     * @param $storeId
     * @param $sender
     *
     * @return bool
     */
	public function sendMail($sendTo, $customer, $emailTemplate, $storeId, $sender,$password = '')
	{


		try {
			/** @var Customer $mergedCustomerData */
			$customerEmailData = $this->customerRegistry->retrieve($customer->getId());
			$customerEmailData->setData('name', $customerEmailData->getName());
			$transport = $this->transportBuilder
				->setTemplateIdentifier($emailTemplate)
				->setTemplateOptions([
					'area'  => Area::AREA_FRONTEND,
					'store' => $storeId,
				])
				->setTemplateVars([
					'customer' => $customerEmailData,
					'password'=>$password
				])
				->setFromByScope($sender, $storeId)
				->addTo($sendTo)
				->getTransport();
			$transport->sendMessage();


			return true;
		} catch (Exception $e) {
			$this->_logger->critical($e->getMessage());
		}

		return false;
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getAutoApproveConfig($storeId = null)
	{
		return $this->getConfigGeneral('auto_approve', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getMessageAfterRegister($storeId = null)
	{
		return $this->getConfigGeneral('message_after_register', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getTypeNotApprove($storeId = null)
	{
		return $this->getConfigGeneral('type_not_approve', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getErrorMessage($storeId = null)
	{
		return $this->getConfigGeneral('error_message', $storeId);
	}

	/**
     * @param null $storeId
     *
     * @return mixed
     */
	public function getCmsRedirectPage($storeId = null)
	{
		return $this->getConfigGeneral('redirect_cms_page', $storeId);
	}

	/**
     * @param $path
     * @param $param
     *
     * @return string
     */
	public function getUrl($path, $param)
	{
		return $this->_getUrl($path, $param);
	}

	/**
     * @param $stringCode
     *
     * @return mixed
     */
	public function getRequestParam($stringCode = null)
	{
		if ($stringCode) {
			return $this->_request->getParam($stringCode);
		}
		return $this->_request->getParams();
	}

	/**
     * Retrieve cookie manager
     *
     * @return     PhpCookieManager
     * @deprecated 100.1.0
     */
	private function getCookieManager()
	{
		if (!$this->cookieMetadataManager) {
			$this->cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
		}

		return $this->cookieMetadataManager;
	}

	/**
     * Retrieve cookie metadata factory
     *
     * @return     CookieMetadataFactory
     * @deprecated 100.1.0
     */
	private function getCookieMetadataFactory()
	{
		if (!$this->cookieMetadataFactory) {
			$this->cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
		}

		return $this->cookieMetadataFactory;
	}

	/**
     * @param null $websiteId
     *
     * @return array|mixed
     */
	public function isEnabledForWebsite($websiteId = null)
	{
		return $this->getConfigValue(
			self::CONFIG_MODULE_PATH . '/general/enabled',
			$websiteId,
			ScopeInterface::SCOPE_WEBSITE
		);
	}

	/**
     * @param $typeApprove
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
	public function shouldEnableButton($typeApprove)
	{
		if (!$this->getRequestParam('id')) {
			return false;
		}

		$customerId = $this->getRequestParam('id');
		$customer   = $this->getCustomerById($customerId);
		$websiteId  = $customer->getWebsiteId();

		if (!$this->isEnabledForWebsite($websiteId) || $this->getIsApproved($customerId) == $typeApprove) {
			return false;
		}

		return true;
	}

	/**
     * @param int $customerId
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
	public function setPendingCustomer($customerId)
	{
		$cusAttributeData = $this->getIsApproved($customerId);
		if (!$cusAttributeData) {
			$actionRegister = false;
			$this->setApprovePendingById($customerId, $actionRegister);
		}
	}

	/**
     * @return bool
     */
	public function hasCustomerEdit()
	{
		$param = $this->_request->getParams();

		return isset($param['customer']['is_active']);
	}

	/**
     * @throws InputException
     * @throws FailureToSendException
     */
	public function processCookieLogout()
	{
		if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
			$metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
			$metadata->setPath('/');
			$this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
		}
	}
}