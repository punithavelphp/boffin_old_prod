<?php

namespace Emc\Otp\Controller\Index;

use Magento\Store\Model\ScopeInterface;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_generateOtp;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Emc\Otp\Model\GenerateOtp $generateOtp
    ) {
        parent::__construct($context);
        $this->_generateOtp = $generateOtp;
    }

    public function execute() {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');
        $data = $this->getRequest()->getPost();
        
         if ($data['resend']) {
            $resend = false;
        } else {
            $resend = true;
        }
		if(isset($data['type']) && $data['type'] == 'CHANGEMOBILE') {
		 $returnArray = $this->_generateOtp->generateOtp($data['mobilenumber'], $data['mobilenumber_code'], 'CHANGEMOBILE', $resend);
        echo json_encode($returnArray[0]);
        die();
		}else{
			 $emailExist = $this->_generateOtp->checkEmail($data['email']);
        if (!$emailExist) {
            $returnArray = array('status'=>false,'message'=>__('Email already Exists'),'valid'=>'Email');
            echo json_encode($returnArray);
            die();
        }
        $returnArray = $this->_generateOtp->generateOtp($data['mobilenumber'], $data['mobilenumber_code'], '', $resend);
        echo json_encode($returnArray[0]);
        die();
		}
       
    }

}
