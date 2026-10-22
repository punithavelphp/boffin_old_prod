<?php

namespace Emc\Otp\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\Dir;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Emc\Otp\Model\SmsLog;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Emc\Otp\Model\Sms;

/**
 * Class SendSms
 * @package Emc\SmsGateway\Helper
 */
class SendSms extends \Emc\Otp\Helper\SmsData
{
    const CONFIG_PATH = 'smsgateway/';
    //const MSG91_WEB_API = 'http://api.msg91.com/api/sendhttp.php?';

    /**
     * @var string
     */
    public $telephoneNum = '';
    public $chars = '';
    public $smsStatus = '';
    public $firstnameOrder = '';
    public $lastnameOrder = '';
    public $orderIncrementId = '';
    public $customerCountry = '';
    public $errorResult = '';
    protected $_customerFactory;
    
    public function __construct(Sms $sms,TimezoneInterface $timezone,SmsLog $smsLogFactory, LoggerInterface $logger, StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig, \Magento\Customer\Model\CustomerFactory $customerFactory)
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig  = $scopeConfig;
        $this->logger       = $logger;
        $this->smsLogFactory = $smsLogFactory;
        $this->timezone = $timezone;
        $this->sms = $sms;
        $this->_customerFactory = $customerFactory;
    }
     
    
    
    /**
     * Message Sending function
     */
    public function sendNotificationBySms($messageType, $gatewayType, $params = null)
    {
        $this->logger->info("SendSms Fn called from SmsGateway Module");
        $this->logger->info("Type: " . $gatewayType);
        $moduleEnabled  = (int) $this->isModuleEnabled();
    //    $gatewayEnabled = (int) $this->isGatewayEnabled($gatewayType);
        $this->logger->info("Module Enabled: " . $moduleEnabled);
        $this->logger->info("Gateway Enabled: " . $gatewayEnabled);
        if ((isset($moduleEnabled) && !empty($moduleEnabled)) && (isset($gatewayEnabled) && !empty($gatewayEnabled))) {
            if ($gatewayType == 'msg91') {
              //return $this->sendSmsMsg91($messageType, $gatewayType, $params);
            } elseif ($gatewayType == 'twilio') {
             //   return $this->sms->sendSmsTwilio($messageType, $gatewayType, $params);
            }
        } else {
            $this->logger->info("Module or Gateway Not Enabled: ");
          //  echo "Module or Gateway Not Enabled:";
        }
    }
    
    public function curl_call($url, $params)
    {
        $this->logger->info("Curl Initialized: ");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        
        if (curl_exec($ch) === false || (!$data)) {
            $this->logger->info("Curl Failed: " . $ch);
            
            return curl_error($ch);
        }
        
        curl_close($ch);
        return $data;
    }
 

   
}

?>
