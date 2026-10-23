<?php

namespace Emc\Otp\Helper;

use Magento\Setup\Exception;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\Dir;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Emc\Otp\Model\SmsLog;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Data
 * @package Emc\SmsGateway\Helper
 */
class SmsData extends AbstractHelper {

    public $configReader;
    public $smsLogFactory;
    public $storeManager;
    public $timezone;

    const CONFIG_PATH = 'smsgateway/';
    const OTP_ON_REGISTRATION_STATUS = 'general/template_settings/otp';
    const ORDER_STATUS = 'general/template_settings/order';
    const SMS_ORDER_TRIGGER_MSG = 'salesorder/';

    public $telephoneNum = '';
    public $chars = '';
    public $smsStatus = '';
    public $orderIncrementId = '';
    public $firstnameOrder = '';
    public $lastnameOrder = '';
    public $customerCountry = '';
    public $errorResult = '';

    public function __construct(TimezoneInterface $timezone, SmsLog $smsLogFactory, StoreManagerInterface $storeManager, \Magento\Framework\Module\Dir\Reader $configReader, \Magento\Framework\App\Helper\Context $context, ScopeConfigInterface $scopeConfig) {
        $this->configReader = $configReader;
        $this->smsLogFactory = $smsLogFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
        parent::__construct($context);
    }

    public function isModuleEnabled() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/enabled');
    }

    public function getConfigValue($field, $storeId = null) {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE);
    }

    public function isGatewayEnabled() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/general_settings/enabled');
    }

    public function getRouterApi() {
         return $this->getConfigValue(self::CONFIG_PATH . 'general/general_settings/route_api');
    }

    public function getAuthkey() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/general_settings/auth_key');
    }

    public function getAuthToken() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/general_settings/auth_token');
    }

    public function getSenderId($code) {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/general_settings/senderid_'.$code);
    }

    public function getUnicode() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/general/text_settings/allow_unicode');
    }

    public function isLogEnabled() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/log');
    }

    public function getChars() {
        return $this->chars;
    }

    public function getLength() {
        $charsPerMsg = $this->getCharsPerMsg();
        $length = ceil($this->chars / $charsPerMsg);
        return $length;
    }

    public function getCharsPerMsg() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/text_settings/chars_per_message');
    }

    public function getSmsStatus() {
        return $this->smsStatus;
    }

    public function getTelephoneFromOrder($order) {
        $this->orderIncrementId = $order->getIncrementId();
        $billingAddress = $order->getBillingAddress();
        $telephone = $billingAddress->getTelephone();
        $this->firstnameOrder = $order->getCustomerFirstname();
        $this->lastnameOrder = $order->getCustomerLastname();
        $this->telephoneNum = $telephone;
        return $telephone;
    }

    public function getCountryMode($country) {
        $countryValue = $this->getConfigValue(self::CONFIG_PATH . 'country_configuration/country_code_enabled');
        if ($country == 'IN') {
            $customerCountry = 91;
        } elseif ($country == 'US') {
            $customerCountry = 1;
        } else {
            $customerCountry = 0;
        }
        $this->customerCountry = $customerCountry;
        return $customerCountry;
    }

    public function getCountry($order) {
        if ($order->getShippingAddress()) {
            return $order->getShippingAddress()->getCountry();
        } else {
            return $order->getBillingAddress()->getCountry();
        }
    }

    /** start of  Order and Regirstration */
    public function isOrderEnabled() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/template_settings/order_placed');
    }

    public function isRegistrationEnabled() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/template_settings/registration');
    }

    /** end of  Order and Regirstration */
    public function getMessage($messageType, $params, $type) {

        switch ($messageType) {
            case 'order':
                return $this->getOrderMessage();
                break;
            case 'registration':
                return $this->getRegisterMessage($params);
                break;
        }
    }

    public function getOrderMessage() {
        $message = $this->getConfigValue(self::CONFIG_PATH . 'general/template_settings/order_placed_message');
        $search = array('{{firstname}}', '{{lastname}}', '{{base_url_sales_order_number}}');
        $replace = array($this->firstnameOrder, $this->lastnameOrder, $this->storeManager->getStore()->getBaseUrl() . self::SMS_ORDER_TRIGGER_MSG . $this->orderIncrementId);
        $message = str_replace($search, $replace, $message);
        $filteredMessage = $this->filterMessage($message);
        $this->chars = strlen($filteredMessage);
        return $filteredMessage;
    }

    public function filterMessage($message) {
        $orderVariable = '{{order_number}}';
        $filteredMessage = str_replace($orderVariable, $this->orderIncrementId, $message);
        if (!$this->isGsmCharactersAllowed()) {
            $gsmSpecialCharArray = array("{", "}", "\\", "[", "]", "~", "|", "^");
            $filteredMessage = str_replace($gsmSpecialCharArray, '', $filteredMessage);
        }
        $charsPerMsg = $this->getCharsPerMsg();
        $finalMsg = (strlen($filteredMessage) > $charsPerMsg) ? substr($filteredMessage, 0, $charsPerMsg) : $filteredMessage;
        return $finalMsg;
    }

    public function isGsmCharactersAllowed() {
        return $this->getConfigValue(self::CONFIG_PATH . 'general/text_settings/allow_gsm_characters');
    }

    public function getRegisterMessage($params) {
        $message = $this->getConfigValue(self::CONFIG_PATH . 'general/template_settings/registration_message');
        $search = array('{{firstname}}', '{{lastname}}');
        $replace = array($params['firstname'], $params['lastname']);
        $message = str_replace($search, $replace, $message);
        $filteredMessage = $this->filterMessage($message);
        $this->chars = strlen($filteredMessage);
        return $filteredMessage;
    }

    public function saveSMSLog($data) {
        try {
            $this->smsLogFactory->setSentDate($this->timezone->formatDate())
                    ->setData($data)
                    ->save();
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function saveLog($order_id = '', $title = '', $type = '', $sender_id = '', $to = '', $recipient = '', $msg = '', $customer_id = 0, $chars = 0, $length = 0, $status = 0) {
        try {
            $this->smsLogFactory->setSentDate($this->timezone->formatDate())
                    ->setOrderNo($order_id)
                    ->setTitle($title)
                    ->setType($type)
                    ->setSenderId($sender_id)
                    ->setTo($to)
                    ->setRecipient($recipient)
                    ->setMessage($msg)
                    ->setCustomerId($customer_id)
                    ->setChars($chars)
                    ->setLength($length)
                    ->setApiStatus($status)
                    ->save();
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

            
    
    public function isSMSEnabled(): bool {
        return (boolean) $this->getConfigValue(self::CONFIG_PATH . self::ORDER_STATUS);
    }

    public function filterGSMChars(string $filteredMessage): string {
        if (!$this->isGsmCharactersAllowed()) {
            $gsmSpecialCharArray = array("{", "}", "\\", "[", "]", "~", "|", "^");
            $filteredMessage = str_replace($gsmSpecialCharArray, '', $filteredMessage);
        }
        $charsPerMsg = $this->getCharsPerMsg();
        $finalMsg = (strlen($filteredMessage) > $charsPerMsg) ? substr($filteredMessage, 0, $charsPerMsg) : $filteredMessage;
        return $finalMsg;
    }

}
