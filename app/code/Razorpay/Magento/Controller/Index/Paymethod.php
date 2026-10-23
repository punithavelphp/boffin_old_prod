<?php

namespace Razorpay\Magento\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

use Magento\Framework\Controller\ResultFactory;

class Paymethod extends Action
{ 
    public $customerSession;
    public $orderFactory;

	/**
     * @var PageFactory
     */
    protected $_resultPageFactory;
	const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     * @param HelperData $helper
     */
    public function __construct(
    Context $context, PageFactory $resultPageFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Sales\Model\OrderFactory $orderFactory
 		
    ) {
        $this->_resultPageFactory = $resultPageFactory;
         $this->customerSession = $customerSession;
         $this->orderFactory = $orderFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
		$url = $this->_url->getUrl();
		$error =1;
		$post = $this->getRequest()->getPostValue();
		if($post && isset($post['order_id'])){
 			$orderId =  $post['order_id'];
			$order = $this->orderFactory->create()->loadByIncrementId($orderId);
			if($order && $order->getId()){
				$error =0;
				if($post['pay_method'] == 'C') {

					$payment = $order->getPayment();
	$payment->setMethod('razorpay');
									$order->setAdminOrderApprovalStatus(1);

	$payment->save();
	$order->save();


					$url = $this->_url->getUrl('razorpay/index/payonline').'?order='.$orderId;
				}else{
	 $payment = $order->getPayment();
	$payment->setMethod('banktransfer');
									$order->setAdminOrderApprovalStatus(0);

	$payment->save();
	$order->save();
					$order->save();
					$url = $this->_url->getUrl('razorpay/index/payoffline').'?order='.$orderId;
				}
			}
		}
		if($error == 1){
			$this->messageManager->addError(__('Some Error Occurs.'));
		}

		header('location:'.$url);die;
		
		
    }
	
	public function sendMail(){
		
		/* try
		https://www.codextblog.com/magento-2/sending-custom-emails-using-custom-module-in-magento-2/
        {
            // Send Mail
            $this->_inlineTranslation->suspend();
                         
            $sender = [
                'name' => $post['name'],
                'email' => $post['email']
            ];
             
            $sentToEmail = $this->_scopeConfig ->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
             
            $sentToName = $this->_scopeConfig ->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
             
             
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier('customemail_email_template')
            ->setTemplateOptions(
                [
                    'area' => 'frontend',
                    'store' => $this->storeManager->getStore()->getId()
                ]
                )
                ->setTemplateVars([
                    'name'  => $post['name'],
                    'email'  => $post['email']
                ])
                ->setFromByScope($sender)
                ->addTo($sentToEmail,$sentToName)
                //->addTo('owner@example.com','owner')
                ->getTransport();
                 
                $transport->sendMessage();
                 
                $this->_inlineTranslation->resume();
                $this->messageManager->addSuccess('Email sent successfully');
                $this->_redirect('customemail/index/index');
                 
        } catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
            $this->_logLoggerInterface->debug($e->getMessage());
            exit;
        } */
		
	}
	
}
