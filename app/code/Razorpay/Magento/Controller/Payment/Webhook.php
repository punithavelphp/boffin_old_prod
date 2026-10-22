<?php

namespace Razorpay\Magento\Controller\Payment;

use Razorpay\Api\Api;
use Razorpay\Api\Errors;
use Razorpay\Magento\Model\Config;
use Razorpay\Magento\Model\PaymentMethod;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Razorpay\Subscription\Helper\SubscriptionWebhook;
use Razorpay\Magento\Helper\Data as UpdatePaymentStatus;
class Webhook extends \Razorpay\Magento\Controller\BaseController
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    protected $api;

    protected $logger;

    protected $quoteManagement;

    protected $objectManagement;

    protected $storeManager;

    protected $customerRepository;

    protected $cache;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    const STATUS_APPROVED = 'APPROVED';

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Razorpay\Model\CheckoutFactory $checkoutFactory
     * @param \Razorpay\Magento\Model\Config $config
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository,
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagement
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Razorpay\Magento\Model\CheckoutFactory $checkoutFactory,
        \Razorpay\Magento\Model\Config $config,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Psr\Log\LoggerInterface $logger
		,UpdatePaymentStatus $updatePaymentStatus
    )
    {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $config
        );

        $keyId                 = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $keySecret             = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);

        $this->api             = new Api($keyId, $keySecret);
        $this->order           = $order;
        $this->logger          = $logger;

        $this->objectManagement   = \Magento\Framework\App\ObjectManager::getInstance();
        $this->quoteManagement    = $quoteManagement;
        $this->checkoutFactory    = $checkoutFactory;
        $this->quoteRepository    = $quoteRepository;
        $this->storeManagement    = $storeManagement;
        $this->customerRepository = $customerRepository;
        $this->eventManager       = $eventManager;
        $this->cache = $cache;
		$this->updatePaymentStatus = $updatePaymentStatus;
    }

    /**
     * Processes the incoming webhook
     */
    public function execute()
    { 
		
		/*
			
	  $post = "{\"entity\":\"event\",\"account_id\":\"acc_ItU9EqHpj4bZw3\",\"event\":\"order.paid\",\"contains\":[\"payment\",\"order\"],\"payload\":{\"payment\":{\"entity\":{\"id\":\"pay_JPaYBplqQ0BByM\",\"entity\":\"payment\",\"amount\":2040408,\"currency\":\"INR\",\"status\":\"captured\",\"order_id\":\"order_JPaY3cTt9f73Rz\",\"invoice_id\":null,\"international\":false,\"method\":\"netbanking\",\"amount_refunded\":0,\"refund_status\":null,\"captured\":true,\"description\":\"#JPaY0vKaxkc056\",\"card_id\":null,\"bank\":\"UTIB\",\"wallet\":null,\"vpa\":null,\"email\":\"punithavelkt@gmail.com\",\"contact\":\"+919786979479\",\"notes\":{\"boffin_weblink\":\"WEBLINkCASE\",\"boffin_order_id\":\"290\"},\"fee\":48155,\"tax\":7346,\"error_code\":null,\"error_description\":null,\"error_source\":null,\"error_step\":null,\"error_reason\":null,\"acquirer_data\":{\"bank_transaction_id\":\"7321906\"},\"created_at\":1651326267}},\"order\":{\"entity\":{\"id\":\"order_JPaY3cTt9f73Rz\",\"entity\":\"order\",\"amount\":2040408,\"amount_paid\":2040408,\"amount_due\":0,\"currency\":\"INR\",\"receipt\":\"379\",\"offer_id\":null,\"status\":\"paid\",\"attempts\":1,\"notes\":{\"boffin_weblink\":\"WEBLINkCASE\",\"boffin_order_id\":\"290\"},\"created_at\":1651326259}}},\"created_at\":1651326281}";
		 */
		 $post = $this->getPostData();
//$post = json_decode($post,TRUE);
   //   $this->logger->info("Razorpay Webhook POST : ".json_encode($post));
        if (json_last_error() !== 0)
        {
           return;
        }
/// $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] = '52x8nnbclbd7v1l7js2got08bzt35l9p671laurfdgqjphrd96';
        if (($this->config->isWebhookEnabled() === true) &&
            (empty($post['event']) === false))
        {
           if (isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) === true)
            {
		 
				$webhookSecret = $this->config->getWebhookSecret();

                //
                // To accept webhooks, the merchant must configure
                // it on the magento backend by setting the secret
                //
                if (empty($webhookSecret) === true)
                {
                    return;
                }

                try
                {
                   $postData = file_get_contents('php://input');
///$postData = $post;
	 
                    $this->rzp->utility->verifyWebhookSignature($postData, $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'], $webhookSecret);
                }
                catch (Errors\SignatureVerificationError $e)
                {
                    $this->logger->warning(
                        $e->getMessage(),
                        [
                            'data'  => $post,
                            'event' => 'razorpay.magento.signature.verify_failed'
                        ]);

                    //Set the validation error in response
                    header('Status: 400 Signature Verification failed', true, 400);
                    exit;
                }

                switch ($post['event'])
                {
                     case 'payment.authorized':
                    case 'order.paid':
                        return $this->orderPaid($post);

                    case 'subscription.charged':
                        if(class_exists(SubscriptionWebhook::class)) {
                            $subscriptionWebhook = new SubscriptionWebhook($this->logger);
                            return $subscriptionWebhook->processSubscriptionCharged($post);
                        }
                    
                    case 'subscription.paused':
                        if(class_exists(SubscriptionWebhook::class)) {
                            $subscriptionWebhook = new SubscriptionWebhook($this->logger);
                            return $subscriptionWebhook->processSubscriptionAction($post);
                        }
                    
                    case 'subscription.resumed':
                        if(class_exists(SubscriptionWebhook::class)) {
                            $subscriptionWebhook = new SubscriptionWebhook($this->logger);
                            return $subscriptionWebhook->processSubscriptionAction($post);
                        }
                        
                    case 'subscription.cancelled':
                        if(class_exists(SubscriptionWebhook::class)) {
                            $subscriptionWebhook = new SubscriptionWebhook($this->logger);
                            return $subscriptionWebhook->processSubscriptionAction($post);
                        }     

                    default:
                        return;
                }
            }
        }

        $this->logger->info("Razorpay Webhook processing completed.");
    }

    /**
     * Order Paid webhook
     *
     * @param array $post
     */
    protected function orderPaid(array $post)
    {
        // Do not process if order is subscription type
        if (isset($post['payload']['payment']['entity']['invoice_id']) === true) {
            $rzpInvoiceId = $post['payload']['payment']['entity']['invoice_id'];
            $invoice = $this->rzp->invoice->fetch($rzpInvoiceId);
            if(isset($invoice->subscription_id)){
                $this->logger->info("Razorpay Webhook: Order is a subscription type, hence exiting from webhook since it is handled separately");
                return;
            }
        }
	 
        $this->logger->info("Razorpay Webhook Event(" . $post['event'] . ")  processing Started.");

        $paymentId  = $post['payload']['payment']['entity']['id'];
        $rzpOrderId = $post['payload']['payment']['entity']['order_id'];

        try
        {
            if($post['event'] === 'payment.authorized')
            {
				
				
		\Magento\Framework\App\ObjectManager::getInstance()
   			->get('Psr\Log\LoggerInterface')->debug(date('h:i:s').'----3---');
                $rzpOrder = $this->getRzpOrder($rzpOrderId);

                $quoteId = $rzpOrder->receipt;

                $rzpOrderAmount = $rzpOrder->amount;

                $amountPaid     = $post['payload']['payment']['entity']['amount'];
            }
            else
            {
				
			
		\Magento\Framework\App\ObjectManager::getInstance()
   			->get('Psr\Log\LoggerInterface')->debug(date('h:i:s').'----2---');
				
                $amountPaid     = $post['payload']['order']['entity']['amount_paid'];

                $rzpOrderAmount = $post['payload']['order']['entity']['amount'];

                $quoteId   = $post['payload']['order']['entity']['receipt'];
            }
			 
			$this->logger->info("Razorpay Webhook: Quote ID-------------------> ".$quoteId);
			$this->logger->info("Razorpay Webhook: Quote ID-------------------> ".json_encode($post));

            if (isset($quoteId) === false)
            {
                $this->logger->info("Razorpay Webhook: Quote ID not set for Razorpay payment_id(:$paymentId)");
                return;
            }
	
            $email   = $post['payload']['payment']['entity']['email'];
            $contact = $post['payload']['payment']['entity']['contact'];
			
			
        }
        catch(\Razorpay\Api\Errors\Error $e)
        {
            $this->logger->critical("Razorpay Webhook: fetching RZP order data(id:$rzpOrderId) PaymentId:(:paymentId) failed with error: ". $e->getMessage());
            return;
        }
        catch(\Exception $e)
        {
            $this->logger->critical("Razorpay Webhook: fetching RZP order data(id:$rzpOrderId) PaymentId:(:paymentId) failed with error: ". $e->getMessage());
            return;
        }

        try
        {
			
			
		\Magento\Framework\App\ObjectManager::getInstance()
   			->get('Psr\Log\LoggerInterface')->debug(date('h:i:s').'----4---');
			
			
            $orderLinkCollection = $this->_objectManager->get('Razorpay\Magento\Model\OrderLink')
                                                       ->getCollection()
                                                       ->addFieldToFilter('quote_id', $quoteId);
			//$this->logger->info('-----payload----------->'.json_encode($post['payload']['payment']['entity']['notes']['boffin_weblink']));

				if(isset($post['payload']['payment']['entity']['notes']['boffin_weblink']) && $post['payload']['payment']['entity']['notes']['boffin_weblink'] == 'WEBLINkCASE') { 
                                                     
				}else{
					 $orderLinkCollection->addFieldToFilter('rzp_order_id', $rzpOrderId);	
				}
                                      $orderLinkCollection =                 $orderLinkCollection ->getFirstItem();

            $orderLink = $orderLinkCollection->getData();
$this->logger->info('-----$orderLink----------->'.json_encode($orderLink));
 
            if (empty($orderLink['entity_id']) === false)
            {
                if ($orderLink['order_placed'])
                {
                     $this->logger->info(__("Razorpay Webhook: Quote order is inactive for quoteID: $quoteId and Razorpay payment_id(:$paymentId) with Maze OrderID (:%1) ", $orderLink['increment_order_id']));

                   // return;
                }

                //set the 1st webhook notification time
                if ($orderLink['webhook_count'] < 1)
                {
                    $orderLinkCollection->setWebhookFirstNotifiedAt(time());
                }

                $paymentSignature = hash_hmac('sha256', $rzpOrderId . "|" . $paymentId, $this->config->getConfigData(Config::KEY_PRIVATE_KEY));
$this->logger->info('---------------->'.$paymentSignature);
				
				
                $orderLinkCollection->setWebhookCount($orderLink['webhook_count'] + 1)
                                    ->setRzpPaymentId($paymentId)
                                    ->setAmountPaid($amountPaid)
                                    ->setRzpSignature($paymentSignature)
                                    ->setEmail($email)
                                    ->setContact($contact)
                                    ->save();
				$this->logger->info('-----$orderLink------ewrrewt----->'.json_encode($orderLink));
								$this->logger->info('-----$orderLink[increment_order_id]----->'.$orderLink['increment_order_id']);


				if(isset($post['payload']['payment']['entity']['notes']['boffin_weblink']) && $post['payload']['payment']['entity']['notes']['boffin_weblink'] == 'WEBLINkCASE' ) { 
 					$this->logger->info('-----WEBKINK CASE----->'.json_encode($orderLink));
					$this->logger->info('-----POST----->'.json_encode($post));
					
 			 	  $this->updatePaymentStatus->updateOrderLinkStatusForPaymentLink($post['payload']['payment']['entity']['notes']['boffin_order_id'],$post);
					$this->logger->info('-----POST----->'.json_encode($post));
 				} 
				 

				

                return;
            }

            $this->logger->info("Razorpay Webhook Event(" . $post['event'] . ") Processed successfully for Razorpay payment_id(:$paymentId): and quoteID(: $quoteId) .");
            return;
        }
        catch (\Exception $e)
        {
            $this->logger->critical("Razorpay Webhook: Quote submitted for order creation with quoteID:$quoteId failed with error: ". $e->getMessage());
            return;
        }
   }

    /**
     * @return Webhook post data as an array
     */
    protected function getPostData() : array
    {
        $request = file_get_contents('php://input');

        return json_decode($request, true);
    }

    /**
     * Get the Order from RZP
     *
     * @param string $orderId
     */
    public function getRzpOrder($orderId)
    {
        try
        {
            $order = $this->api->order->fetch($orderId);

            return $order;
        }
        catch(\Razorpay\Api\Errors\Error $e)
        {
            $this->logger->critical("Razorpay Webhook: fetching RZP order data(id:$orderIdorderId) failed with error: ". $e->getMessage());
            return;
        }
        catch(\Exception $e)
        {
            $this->logger->critical("Razorpay Webhook: fetching RZP order data(id:$orderId) failed with error: ". $e->getMessage());
            return;
        }
    }
}