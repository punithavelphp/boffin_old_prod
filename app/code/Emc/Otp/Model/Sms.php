<?php

/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_SmsGateway
 * @author  Sai Ram <sairam@egrovesys.com>
 */
declare(strict_types = 1);

namespace Emc\Otp\Model;

use Psr\Log\LoggerInterface;
use Emc\Otp\Helper\SmsData as Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class Sms
 *
 * @package Emc\SmsGateway\Model
 */
class Sms  {

	const MODULE_NOT_ENABLED = 'Module or Gateway Not Enabled:';
	const XML_PATH_GATEWAY_TYPE = 'smsgateway/general/gateway';

	//const MSG91_WEB_API = 'http://control.msg91.com/api/sendhttp.php?';

	/**
     * @var LoggerInterface
     */
	protected $logger;

	/**
     * @var Data
     */
	protected $data;

	/**
     * Sms constructor.
     *

     * @param LoggerInterface $logger
     * @param Data $data
     */
	public function __construct(
		LoggerInterface $logger, Data $data
	) {

		$this->logger = $logger;
		$this->data = $data;
	}

	/**
     * @inheritdoc
     */
	public function formatMessages($message, $replaceData = null) {
		if (!empty($replaceData)) {
			foreach ($replaceData as $key => $data) {
				$message = str_replace($key, $data, $message);
			}
		}
		return $message;
		return $this->data->filterGSMChars($message);
	}

	/**
     * @inheritdoc
     */
	public function sendSms($code, $telephone, $message, $replaceData = null): bool {

		$message = $this->formatMessages($message, $replaceData);

		$status = false;
		try {
			$moduleEnabled = (int) $this->data->isModuleEnabled();
			if (1) {
				$code = str_replace('+','',$code);
				$telephone = str_replace('+','',$telephone);
				$telephone = preg_replace('/\s+/', '', $telephone);
				$code = $this->getCodeForCountry(trim($code));
				if($code == 'GUEST'){
					$code= '';
				}
				if($code == ''){
					$code = '91';	
				}


				$data['message'] = $message;
				$data['to'] = $telephone;
				// $data['customer_id'] = $customerId;
				//  $data['recipient'] = $customerName;
				$data['sender_id'] = $telephone;
				$data['type'] = 'OTP';
				$this->data->saveSMSLog($data);


				try {

					$status = $this->sendSMSCountry($code, $telephone, $message);

				} catch (\Exception $e) {
					throw new LocalizedException(
						new Phrase(
							'Some Error Occurs. Please try again later.'
						)
					);  
				}
				//$this->sendSMSUsingMsg91($MSG91_WEB_API, $params);
			} else {
				$this->logger->info(self::MODULE_NOT_ENABLED);
				return false;
			}
		} catch (\Exception $e) {
			throw new LocalizedException(
				new Phrase(
					$e->getMessage()
				)
			); 
		}

		return $status;
	}

	public function getCodeForCountry($country_code){
		if($country_code == 'IN') {
			$country_code = '91';
		} 
		$country_code = '91';

		return $country_code;
	}

	public function sendSMSCountry($code, $to, $message) {





		/* $authkey = $this->data->getAuthkey();
        $authToken = $this->data->getAuthToken();

        $senderId = $this->data->getSenderId($code);

        $url = $this->data->getRouterApi();

		$senderId = 'ABSBOFF';
        $data = [
            'SenderId' => $senderId,
            'Number' =>   $to,
            'Text' => $message
        ]; */
		//   $authorization = base64_encode($authkey . ':' . $authToken);
		/* $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization : Basic ' . $authorization,
            'Content-Type: application/json'
        ]);


        $response = curl_exec($curl);
        curl_close($curl);
         */
		$this->logger->info("OTP MESSAGE: ".$message);

		$url = "https://sms.aspirentech.com/httpapi/httpapi?token=695914a4f7ce456c9cb7b21852ec9812&sender=BOFFIN&number=".$to."&route=2&type=1&sms=".urlencode($message)."&templateid=1207164872512420934";	
		$response = file_get_contents($url);

		$this->logger->info("OTP URL : ".$url);

		//	$url = 'http://sms.aspirentech.com/httpapi/otpapi?token=82e81615e9b7bde777a8e721274417b0&number='.$to.'&sms='.urlencode($message);
		/*  $curl = curl_init();
         curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'get',
          //  CURLOPT_POSTFIELDS => json_encode($data),
         //   CURLOPT_HTTPHEADER => array(
        //        'Authorization: Basic ' . $authorization,
        //        'Content-Type: application/json'
        //    ),
        ));

        $response = curl_exec($curl);
         curl_close($curl);
 		 */
		$this->logger->info("Curl RESPONSE: ".$response);

		if ( is_numeric($response) ) {
			return true;
		} else {
			return false;
		}


	}

	public function sendSmsOthers($to, $message,$type='delivery') {
return true;
  		$this->logger->info("DELIVERY MESSAGE: ".$message);
 		$url = "https://sms.aspirentech.com/httpapi/httpapi?token=695914a4f7ce456c9cb7b21852ec9812&sender=BOFFIN&number=".$to."&route=2&type=1&sms=".urlencode($message)."&templateid=1207165367677047287";	
		$response = file_get_contents($url);
 		$this->logger->info("DELIVERY URL : ".$url);
 		$this->logger->info("Curl RESPONSE: ".$response);
 		if ( is_numeric($response) ) {
			return true;
		} else {
			return false;
		}
 	}

	public function sendSMSUsingMsg91($url, $params) {
		$this->logger->info("Curl Initialized: ");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		//  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
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
