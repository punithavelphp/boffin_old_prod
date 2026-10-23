<?php

declare(strict_types=1);

namespace Emc\Otp\Model;

use Emc\Otp\Api\GenerateOtpInterface;
use Emc\Otp\Model\ResourceModel\EmcOtpFactory as EmcOtpResource;
use Emc\Otp\Model\EmcOtpCountFactory as EmcOtpCountFactory;
use Emc\Otp\Model\ResourceModel\EmcOtp\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Emc\Otp\Model\Sms as SmsInterface;
use Emc\Otp\Helper\SmsData as Data;
use Emc\Otp\Helper\Data as OtpHelper;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as customerCollectionFactory;

/**
 * Class GenerateOtp
 *
 * @package Emc\Otp\Model
 */
class GenerateOtp implements GenerateOtpInterface {
    public $emcOtpCountFactory;
    public $_scopeConfig;


    const VALIDATE_TELEPHONE_NUMBER_VALID = 'Please enter valid Mobile number';
    const VALIDATE_TELEPHONE_NUMBER = 'Please enter Mobile number';
    const XML_PATH_OTP_MESSAGE = "smsgateway/general/template_settings/otp_message";
    const REQUIRED_FIELDS_ERROR_MESSAGE = 'Please enter all the required fields';
    const INVALID_EMAIL = 'Please enter a valid email address';
    const NUMBERS = '1357902468';
    const OTP_CHARACTERS_LENGTH = 4;
    const FORGOT_PASSWORD = 'forgot_password';
    const REGISTER = 'register';
    const MOBILE_NUMBER_NOT_ASSOCIATED = ' Mobile number has been not associated with any of the existed accounts';
    const MOBILE_NUMBER_EXITS = 'Mobile Number already Exists';
    const VALIDATE_OTP = 'Please enter a valid OTP';
    const OTP_EXPIRY_TIME = 5;
    const VALIDATE_OTP_EXPIRY = "OTP has been expired";

    /**
     * @var EmcOtpFactory
     */
    protected $emcOtpFactory;

    /**
     * @var EmcOtpResource
     */
    protected $emcOtpResource;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SmsInterface
     */
    protected $smsInterface;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var MobileLoginHelper
     */
    protected $OtpHelper;

    /**
     * @var customerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * GenerateOtp constructor.
     *
     * @param EmcOtpFactory $emcOtpFactory
     * @param EmcOtpResource $emcOtpResource
     * @param CollectionFactory $collectionFactory
     * @param SmsInterface $smsInterface
     * @param Data $data
     * @param OtpHelper $OtpHelper
     * @param customerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
            EmcOtpFactory $emcOtpFactory
            , EmcOtpCountFactory $emcOtpCountFactory
            , EmcOtpResource $emcOtpResource
            , CollectionFactory $collectionFactory
            , SmsInterface $smsInterface
            , Data $data
            , OtpHelper $OtpHelper
            , customerCollectionFactory $customerCollectionFactory
		,\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->emcOtpFactory = $emcOtpFactory;
        $this->emcOtpCountFactory = $emcOtpCountFactory;
        $this->emcOtpResource = $emcOtpResource;
        $this->collectionFactory = $collectionFactory;
        $this->smsInterface = $smsInterface;
        $this->data = $data;
        $this->OtpHelper = $OtpHelper;
        $this->customerCollectionFactory = $customerCollectionFactory;
		$this->_scopeConfig = $scopeConfig;

    }

    public function checkEmail($email) {
        $customerDetails = $this->customerCollectionFactory->create()
                ->addAttributeToFilter('email', $email);
        if ($customerDetails->getData()) {
            return false;
        }
        return true;
    }

	 public function getConfig($path) {
		 $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
return $this->_scopeConfig->getValue($path, $storeScope);
	 
	 }
    public function generateOtp($telephone, $code = null, $type = 'OTP', $resend = false) {
 		
	 
		$code = $this->smsInterface->getCodeForCountry($code);
        $message = array();
        $message['valid'] = 'Mobile';
       try {
            $customerDetails = $this->customerCollectionFactory->create()
                    ->addAttributeToFilter('mobilenumber', $telephone);

            if ($customerDetails->getData() && ($type == null || strtolower($type) == 'otp')) {
                $message['status'] = false;
                $message['message'] = 'Mobile Number already Exists';
                return array($message);
            } else {
                
            }
            $this->validateTelephoneNumber($telephone, $code);
            $data = $this->saveOtpDetails($telephone, $code, $resend);
            
            if ($data === false) {
				
                $message['status'] = false;
				$message['exceed'] = true;
                $message['message'] = __('Exceeded Otp Request. Please try again after 2 Hrs');
                return array($message);
            }
            //  $telephoneNumber = $code.$telephone;
            $status = $this->sendSms($code, $telephone, $data['otp']);
            if (0 && !$status) {
                $message['status'] = false;
				$message['exceed'] = false;
                $message['message'] = __('Some Error Occurs. Contact Administrator.');
				$message['status'] = false;
                return array($message);
            }
         } catch (\Exception $e) {
            $message['status'] = false;
		   $message['exceed'] = false;
            $message['message'] = $e->getMessage();
             $message['e_message'] = __('Some Error Occurs. Please try again later');
            return array($message);
        }  
        $message['status'] = true;
		$message['exceed'] = false;
        $message['message'] = __('Otp has been Sent to  ' . $code . ' ' . $telephone);
        return array($message);
    }

    /**
     * Checks the whether the mobile number already exists
     *
     * @param string $telephone
     * @return \Magento\Framework\DataObject
     */
    protected function updateAlreadyExist(string $telephone) {
        $emcOtpCollection = $this->collectionFactory->create();
        $otpDetails = $emcOtpCollection->addFieldToFilter('mobile_number', $telephone)->addFieldToFilter('status', 0);
        foreach ($otpDetails as $otpDetail) {
            $otpDetail->setStatus(1)->save();
        }
        return $otpDetails;
    }

    protected function checkAlreadyExist(string $telephone) {
        $emcOtpCollection = $this->collectionFactory->create();
        $otpDetails = $emcOtpCollection->addFieldToFilter('mobile_number', $telephone)->addFieldToFilter('status', 0);
        foreach ($otpDetails as $otpDetail) {
            $otpDetail->setStatus(1)->save();
        }
        return $otpDetails;
    }

    /**
     * Saves the new generated OTP
     *
     * @param array $data
     */
    protected function saveOtp(array $data) {
        /** @var \Emc\Otp\Model\EmcOtp $emcOtpModel */
        $emcOtpModel = $this->emcOtpFactory->create(['data' => $data]);
        /** @var \Emc\Otp\Model\ResourceModel\EmcOtp $emcOtpResourceModel */
        $emcOtpResourceModel = $this->emcOtpResource->create();
        $emcOtpResourceModel->save($emcOtpModel);
        return $emcOtpModel;
    }

    /**
     * Validate the Telephone number
     *
     * @param string $telephone
     * @throws \Zend_Validate_Exception
     */
    protected function validateTelephoneNumber(string $telephone, $code) {
        if (empty($telephone)) {
            throw new LocalizedException(
                            new Phrase(
                                    self::VALIDATE_TELEPHONE_NUMBER
                            )
            );
        }
        if ($code == '91' && !$this->validateMobile($telephone)) {

            throw new LocalizedException(
                            new Phrase(
                                    self::VALIDATE_TELEPHONE_NUMBER_VALID
                            )
            );
        }
    }

    protected function validateMobile($mobile) {
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }

    /**
     * Send Sms Using the gateway configured
     *
     * @param $telephone
     * @param $otp
     */
    protected function sendSms($code, $telephone, $otp) {
        $message = $this->data->getConfigValue(
                self::XML_PATH_OTP_MESSAGE
        );
        /**
         * The string need to be formatted in message
         */
		
	//	$message = 'Your OTP Key {{otp}}';
         $replaceData = [
            "{{otp}}" => $otp
        ];
        return $this->smsInterface->sendSms($code, $telephone, $message, $replaceData);
    }

    protected function checkOtpCount($telephone) {
        $otpCountDetails = $this->emcOtpCountFactory->create()->getCollection()->addFieldToFilter('mobile_number', $telephone)->setOrder('id', 'ASC');
        return $otpCountDetails;
    }

    protected function saveOtpDetails($telephone, $code = null, $resend = false) {
		
		$MAX_OTP_COUNT = $this->_scopeConfig->getValue('otp_settings/general_configuration/max_count');
		$DIFF_TIMER = $this->_scopeConfig->getValue('otp_settings/general_configuration/reset_interval');
        $otpDetails = $this->updateAlreadyExist($telephone);
        $count = 0;
        $otpCountData = $this->checkOtpCount($code . '' . $telephone);
        
        if ($otpCountData->getSize()) {
            foreach ($otpCountData as $cData) {
                $created_time = $cData->getData('created_at');
                $count = $cData->getData('count');
		$countData = $cData; 
            }
            $to_time = strtotime(date('Y-m-d H:i:s'));
            $from_time = strtotime($created_time);
            $diff = round(abs($to_time - $from_time) / 60, 2);
            if ($count > $MAX_OTP_COUNT && $diff < $DIFF_TIMER) {
                return false;
            } else {
                if ($count > $MAX_OTP_COUNT && $diff > $DIFF_TIMER) {
                    $count = 0;
                }
                $countData->setData('count', ($count + 1));
                $countData->setData('created_at', date('Y-m-d H:i:s'));
                $countData->save();
            }
				 
        } else {
			$countData = $this->emcOtpCountFactory->create();
            $countData->setData('mobile_number', $code . '' . $telephone);
            $countData->setData('count', ($count + 1));
            $countData->setData('created_at', date('Y-m-d H:i:s'));
            $countData->save();
        }
        $otpString = $this->getOtp();
        $data = [
            'mobile_number' => $telephone,
            'created_at' => date('Y-m-d H:i:s'),
            'otp' => $otpString,
            'status' => 0,
            'telephone_code' => $code,
            'customer_email' => ''
        ];
        $result = $this->saveOtp($data);
        return $result->getData();
    }

    /**
     * @return string
     */
    protected function getOtp(): string {
	//	return '1234';
        $characterTypes = [
            [
                'characters' => self::NUMBERS,
                'length' => self::OTP_CHARACTERS_LENGTH
            ]
        ];
        return $this->OtpHelper->generateRandomString($characterTypes);
    }

    public function validateOtp($telephoneNumber, $otp) {
        $otpCollection = $this->collectionFactory->create();
        // filer the item by telephone and email
        $otpCollection->addFieldToFilter('mobile_number', $telephoneNumber)
                ->addFieldToFilter('otp', $otp)
                ->addFieldToFilter('status', 0);
        if ($otpCollection->getSize()) {
            $data = $otpCollection->getFirstItem();
            $createdAt = strtotime($data->getCreatedAt());
            $currentDate = strtotime(date('Y-m-d H:i:s'));
            $interval = abs($currentDate - $createdAt);
            $minutes = round($interval / 60);
            $data = [
                'id' => $data->getId(),
                'status' => 1
            ];
            $otpModel = $this->emcOtpFactory->create(['data' => $data]);
            /** @var \Emc\Otp\Model\ResourceModel\EmcOtp $otpResourceModel */
            $otpResourceModel = $this->emcOtpResource->create();
            if ($minutes < self::OTP_EXPIRY_TIME) {
                /** @var \Emc\Otp\Model\EmcOtp $otpModel */
                $otpResourceModel->save($otpModel);
                $message['status'] = true;
                $message['message'] = __('Otp Verified');
            } else {
                $otpResourceModel->save($otpModel);
                $message['status'] = false;
                $message['message'] = 'OTP Expired';
            }
        } else {
            $message['status'] = false;
            $message['message'] = self::VALIDATE_OTP;
        }
        return array($message);
    }

}
