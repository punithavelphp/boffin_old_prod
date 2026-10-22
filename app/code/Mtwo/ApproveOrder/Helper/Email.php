<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Mtwo\ApproveOrder\Helper;

use Magento\Framework\Exception\MailException;

/**
 * Webkul Marketplace Helper Email.
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
	const XML_PATH_EMAIL_PAYMENT_LINK = 'payment_settings/custom/email_template';
	const XML_PATH_EMAIL_ORDER_DENY = 'payment_settings/custom/deny_email_template';
	const XML_PATH_EMAIL_FACULTY_INFO = 'payment_settings/custom/faculty_email_template';
	const XML_PATH_EMAIL_FACULTY_STUDENT = 'payment_settings/custom/faculty_approve_student_template';
 
	/**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
	protected $_inlineTranslation;

	/**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
	protected $_transportBuilder;

	protected $_template;

	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;

	protected $_messageManager;

	/**
     * @param Magento\Framework\App\Helper\Context              $context
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder
     * @param Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param Session                                           $customerSession
     */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Model\Session $customerSession

	) {
		parent::__construct($context);
		$this->_inlineTranslation = $inlineTranslation;
		$this->_transportBuilder = $transportBuilder;
		$this->_storeManager = $storeManager;
		$this->_messageManager = $messageManager;
		$this->customerFactory = $customerFactory;
		$this->_customerSession = $customerSession;
		$this->objectManagement   = \Magento\Framework\App\ObjectManager::getInstance();
	}

	/**
     * Return store configuration value.
     *
     * @param string $path
     * @param int    $storeId
     *
     * @return mixed
     */
	protected function getConfigValue($path, $storeId)
	{
		return $this->scopeConfig->getValue(
			$path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			$storeId
		);
	}

	/**
     * Return store.
     *
     * @return Store
     */
	public function getStore()
	{
		return $this->_storeManager->getStore();
	}

	/**
     * Return template id.
     *
     * @return mixed
     */
	public function getTemplateId($xmlPath)
	{
		return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
	}

	public function websitePaymentLink(){
		return 'https://boffinbutler.com/';
	}

	/**
     * [generateTemplate description].
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     */
	public function generateTemplate($emailTemplateVariables, $receiverInfo,$senderInfo=array())
	{
					 			$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();

									$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog('-->generateTemplate $receiverInfo-->'.json_encode($receiverInfo));

		
		
		if(!isset($receiverInfo['email'])){
		return;	
		}
		$storeName = $this->getStore()->getName();
		$templateVars = array(
			'store_name' => $storeName
		);

		$emailTemplateVariables = array_merge($emailTemplateVariables,$templateVars);

		$adminEmail = $this->getConfigValue(
			'trans_email/ident_general/email',
			$this->getStore()->getStoreId()
		);




		if(count($senderInfo)){
			$senderEmail = $senderInfo['email'];
		}else{
			$senderInfo['name'] = $storeName;
			$senderEmail = $adminEmail;

		}
		$senderInfo['email'] = $adminEmail;
		
		
		
		$template = $this->_transportBuilder->setTemplateIdentifier($this->_template)
			->setTemplateOptions(
			[
				'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
				'store' => $this->_storeManager->getStore()->getId(),
			]
		)
			->setTemplateVars($emailTemplateVariables)
			->setFrom($senderInfo)
			->addTo($receiverInfo['email'], $receiverInfo['name'])
			->setReplyTo($senderEmail, $senderInfo['name']);
		return $this;
	}

	public function generateAndSendPaymentLink($order){
		$purchaseDepartmentExist = false;
		$currentCustomer =  $this->_customerSession->getCustomer();
		
		if(!$currentCustomer->getId()){
			return;	
		}
		$institute = $currentCustomer->getInstitute();
		$department = $currentCustomer->getDepartment();
		$faculty = $currentCustomer->getFaculty();
		if($institute != '' && $department != '' ) {
			$customers = $this->customerFactory->create()->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('institute',$institute)
				->addAttributeToFilter('department',$department)
				->addAttributeToFilter('group_id',array(7)); //purchase dept

			if($customers->getSize()){
				$purchaseDepartmentExist = true;
				foreach($customers as $customerData){
					$customer_firstname = $customerData->getFirstname();
					$customer_lastname = $customerData->getLastname();
					$customer_email = $customerData->getEmail();
					$mobilenumber = $customerData->getMobilenumber();
					$this->SendMailWithCustomData($order,$customer_firstname,$customer_lastname,$customer_email,$mobilenumber);	
				}
			}
		}else if($faculty != ''){
			$customers = $this->customerFactory->create()->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('faculty',$faculty)
				->addAttributeToFilter('group_id',array(6)); //purchase dept

			//	echo $customers->getSelect();
			//	die;
			if($customers->getSize()){
				$purchaseDepartmentExist = true;
				foreach($customers as $customerData){
					$customer_firstname = $customerData->getFirstname();
					$customer_lastname = $customerData->getLastname();
					$customer_email = $customerData->getEmail();
					$mobilenumber = $customerData->getMobilenumber();
					$this->SendMailWithCustomData($order,$customer_firstname,$customer_lastname,$customer_email,$mobilenumber);	
				}
			}


		}
		if(!$purchaseDepartmentExist){
			$customer_firstname = $currentCustomer->getFirstname();
			$customer_lastname = $currentCustomer->getLastname();
			$customer_email = $currentCustomer->getEmail();
			$mobilenumber = $currentCustomer->getMobilenumber();
			$this->SendMailWithCustomData($order,$customer_firstname,$customer_lastname,$customer_email,$mobilenumber);	
		}

		/*$emailTemplateVariables = array(
				'customer_name'=>$order->getCustomerFirstname().' '.$order->getCustomerLastname(),
				'order_number'=>$order->getIncrementId(),
				'payment_amount'=>$order->getGrandTotal(),
				'payment_link'=>'https://google.com'
			);
			$path = self::XML_PATH_EMAIL_PAYMENT_LINK;
		$receiverInfo['name'] = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
		$receiverInfo['email'] = $order->getCustomerEmail();
			*/
	}



	public function sendDeliveredMailAndSms($order,$shipmentId){
	/*	$currentCustomer =  $this->_customerSession->getCustomer();

		$customer_firstname = $order->getCustomerFirstname();
		$customer_lastname = $order->getCustomerLastname();
		$customer_email = $currentCustomer->getCustomerEmail();


		$emailTemplateVariables = array(
			'customer_name'=>$customer_firstname.' '.$customer_lastname,
			'order_number'=>$order->getIncrementId(),
			'shipment_number'=>$shipmentId,
			'item'=> 
		);
		$path = self::XML_PATH_EMAIL_PAYMENT_LINK;
		$receiverInfo['name'] = $customer_firstname.' '.$customer_lastname;
		$receiverInfo['email'] = $customer_email;
		return $this->sendMail($emailTemplateVariables,$receiverInfo,$path);
*/

	}



	function SendMailWithCustomData($order,$customer_firstname,$customer_lastname,$customer_email,$mobilenumber = ''){
	 

		$jsonData = $this->objectManagement->get('Razorpay\Magento\Controller\Index\Payonline')->jsonExecute($order->getIncrementId(),$mobilenumber,$customer_email);


		$emailTemplateVariables = array(
			'customer_name'=>$customer_firstname.' '.$customer_lastname,
			'order_number'=>$order->getIncrementId(),
			'payment_amount'=>$order->getGrandTotal(),
			'payment_link'=>$jsonData['url']
		);
		$path = self::XML_PATH_EMAIL_PAYMENT_LINK;
		$receiverInfo['name'] = $customer_firstname.' '.$customer_lastname;
		$receiverInfo['email'] = $customer_email;
		return $this->sendMail($emailTemplateVariables,$receiverInfo,$path);
	}
	public function sendMailtoFaculty($order){
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('msg to print');
//die;
		$student = $this->customerFactory->create()->load($order->getCustomerId());
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($student->getId());

		$faculty = $this->customerFactory->create()->load($student->getFaculty());

		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($faculty->getId());

		$customer_firstname = $faculty->getFirstname();
		$customer_lastname = $faculty->getLastname();
		$customer_email = $faculty->getEmail();
		$emailTemplateVariables = array(
			'customer_name'=>$customer_firstname.' '.$customer_lastname,
			'order_number'=>$order->getIncrementId(),
			'student_name'=>$order->getCustomerFirstname().' '.$order->getCustomerLastname()
		);
		if($customer_firstname != ''){
			
		}else{
			return;	
		}


		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(json_encode($emailTemplateVariables));

		$path = self::XML_PATH_EMAIL_FACULTY_INFO;
		$receiverInfo['name'] = $customer_firstname.' '.$customer_lastname;
		$receiverInfo['email'] = $customer_email;
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(json_encode($receiverInfo));

		return $this->sendMail($emailTemplateVariables,$receiverInfo,$path);
	}



	public function sendOrderDenyEmail($order){
		$emailTemplateVariables = array(
			'customer_name'=>$order->getCustomerFirstname().' '.$order->getCustomerLastname(),
			'order_number'=>$order->getIncrementId()

		);

		$receiverInfo['name'] = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
		$receiverInfo['email'] = $order->getCustomerEmail();
		$path = self::XML_PATH_EMAIL_ORDER_DENY;
		return $this->sendMail($emailTemplateVariables, $receiverInfo,$path);	
	}
	
	
	public function sendFacultyApproveStudentMail($student,$faculty,$isCreate,$password = ''){
			 			$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();
if($isCreate){
	$loginDetails = 0;	
}else{
	$loginDetails = 1;	
}
		
		$emailTemplateVariables = array(
			'student_name'=>$student->getData('firstname').' '.$student->getData('lastname'),
			'customer_name'=>$faculty->getData('firstname').' '.$faculty->getData('lastname'),
			'link'=>$this->getStore()->getBaseUrl()."approvecustomer/",
			'iscreate'=>$loginDetails,
			'username'=>$faculty->getData('email'),
			'password'=>$password
		);
		 
			$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog('-->$emailTemplateVariables-->'.json_encode($emailTemplateVariables));

		$receiverInfo['name'] = $faculty->getData('firstname').' '.$faculty->getData('lastname');
		$receiverInfo['email'] = $faculty->getData('email');
					$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog('-->$receiverInfo-->'.json_encode($receiverInfo));

		$path = self::XML_PATH_EMAIL_FACULTY_STUDENT;
							$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog('-->$path-->'.$path);

		return $this->sendMail($emailTemplateVariables, $receiverInfo,$path);	
	}

	public function sendMail($emailTemplateVariables, $receiverInfo,$path,$senderInfo = array())
	{
 		 	 			 			$objectManager     = \Magento\Framework\App\ObjectManager::getInstance();

	 	try {
			$this->_template = $this->getTemplateId($path);
							$objectManager->get('\AL\CustomLog\Helper\Log')->dmsLog('-->$this->_template-->'.$this->_template);

			$this->_inlineTranslation->suspend();
			$this->generateTemplate($emailTemplateVariables, $receiverInfo);
			$transport = $this->_transportBuilder->getTransport();
			$transport->sendMessage();
	 	} catch (\Exception $e) {
	 
 			echo $e->getMessage();
	 	 
	 		$this->_messageManager->addError($e->getMessage());
	 	}
		$this->_inlineTranslation->resume();
	}

}
