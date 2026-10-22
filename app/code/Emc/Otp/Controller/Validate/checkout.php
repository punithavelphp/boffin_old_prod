<?php

namespace Emc\Otp\Controller\Validate;

use Magento\Store\Model\ScopeInterface;

class Checkout extends \Magento\Framework\App\Action\Action {

    protected $_generateOtp;
    protected $_customerSession;

    public function __construct(
            \Magento\Framework\App\Action\Context $context
            , \Emc\Otp\Model\GenerateOtp $generateOtp
            , \Magento\Customer\Model\Session $_customerSession
            , \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->_generateOtp = $generateOtp;
        $this->_customerSession = $_customerSession;
        $this->customerRepository = $customerRepository;
    }

    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');
        $data = $this->getRequest()->getPost();
        if (isset($data['otp_arr'])) {
            $data['otp'] =  $data['otp_arr'];
        }

        $returnArray = $this->_generateOtp->validateOtp($data['mobilenumber'], $data['otp']);
        
        echo json_encode($returnArray[0]);
        die();
    }

}
