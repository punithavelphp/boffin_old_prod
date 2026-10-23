<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mtwo\Refrens\Controller\Adminhtml\Index;

 
class Email extends \Magento\Backend\App\Action
{
	public $refrensFactory;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_invoice';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
		,\Mtwo\Refrens\Model\RefrensFactory $refrensFactory
    ) {
        parent::__construct($context);
		$this->refrensFactory = $refrensFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Notify user
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if (!$invoiceId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $invoice = $this->_objectManager->create(\Magento\Sales\Api\InvoiceRepositoryInterface::class)->get($invoiceId);
        if (!$invoice) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
		
		$invoiceDataExist = $this->refrensFactory->create()->getCollection()->addFieldToFilter('invoice_id',$invoiceId)->getFirstItem();
		$refrens_invoice_id = '65794badff4c2300405e580f';
	/*	if($invoiceDataExist->getId()){
			$refrens_invoice_id = $invoiceDataExist->getData('refrens_invoice_id');
		}else{
			 $this->messageManager->addErrorMessage(__('Please print the invoice to send invoice details to refrens. On printing the same will send email for the first time.'));
        return $this->resultRedirectFactory->create()->setPath(
            'sales/invoice/view',
            ['order_id' => $invoice->getOrder()->getId(), 'invoice_id' => $invoiceId]
        );
		} */
		

         /// connect API and Send Invoice.
		$authUrl = 'https://api.refrens.com/authentication';
		$data = array();
		$data['strategy'] = 'sandbox-punith';
		$data['appSecret'] = 'KBwIqK9TqaYu2QBQ2fPWsMcntdD5NX';
		$data['appId'] = 'sandbox-punith';
	 	$data['strategy'] = 'app-secret';
	 	$data['appSecret'] = 'jWP4GhKYz3uIqd5YDEjmz3g05Pjom0';
	 	$data['appId'] = 'boffin-butler-pvt-ltd';
		$authKeyArray = $this->curl($authUrl,$data);
		$authKeyArray = json_decode($authKeyArray);

	 
		 	if(isset($authKeyArray->accessToken) && !empty($authKeyArray->accessToken))
 			{
			  $accessToken = $authKeyArray->accessToken;
				$orderData = $invoice->getOrder();
				$customer_email = $orderData->getCustomerEmail();
				$customer_name = $orderData->getCustomerFirstname().' '.$orderData->getCustomerLastname();
				$emailUrl = 'https://api.refrens.com/businesses/boffin-butler-pvt-ltd/invoices/'.$refrens_invoice_id.'/email';
			 /* "cc" => [
						[
							"email" => "punithavelkt+ccref@gmail.com", 
							"name" => "CC Name" 
						] 
					],  */
				$emailArray = [
 					"to" => [
						"email" => $customer_email, 
						"name" => $customer_name
					], 
					"from" => [
						"name" => "BOFFIN BUTLER PRIVATE LIMITED" 
					] 
				]; 
 
				$refrens_email = $this->curl($emailUrl,$emailArray,$accessToken);
				$refrens_email = json_decode($refrens_email);
				$this->messageManager->addSuccessMessage(__('Email Sent to '.$customer_name.' ('.$customer_email. ').' ));
				
			}else{
				 $this->messageManager->addErrorMessage(__('Error in Api Authentication.'));
			}
		

       
        return $this->resultRedirectFactory->create()->setPath(
            'sales/invoice/view',
            ['order_id' => $invoice->getOrder()->getId(), 'invoice_id' => $invoiceId]
        );
    }
	
	
	public function curl($url,$data,$authkey = ''){
			
			$curl = curl_init();
			$header[] = 'Content-Type: application/json';
			
			if($authkey != '') {
				$header[] = 'Authorization: Bearer '.$authkey;
			}
			
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => $header
			));
			
			$response = curl_exec($curl);
			
			curl_close($curl);
			return  $response;
			
		}
	
	
}
