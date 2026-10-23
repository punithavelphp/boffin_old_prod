<?php
namespace Mtwo\Quote\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action
{
	public $_customerSession;
	public $logger;
	public $quote;
	public $quoteItems;
	public $resultJsonFactory;
	public $product;
	public $mproduct;
	public $mailHelper;
	public $urlInterface;

	/**
     * @var PageFactory
     */
	protected $_resultPageFactory;


	/**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
	public function __construct(
		Context $context
		, PageFactory $resultPageFactory
 		, \Magento\Customer\Model\Session $customerSession
   		, \Psr\Log\LoggerInterface  $logger
		, \Mtwo\Quote\Model\Quote $quote
		, \Mtwo\Quote\Model\QuoteItems $quoteItems
		,JsonFactory $resultJsonFactory
		,\Magento\Catalog\Model\Product $product
		, \Webkul\Marketplace\Model\Product $mproduct
		, \Mtwo\Quote\Helper\MailHelper $mailHelper
,\Magento\Framework\UrlInterface $urlInterface
	) {
		$this->_resultPageFactory = $resultPageFactory;
 		$this->_customerSession = $customerSession;
 		$this->logger = $logger;
		$this->quote = $quote;
		$this->quoteItems = $quoteItems;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->product = $product;
		$this->mproduct = $mproduct;
		$this->mailHelper = $mailHelper;
		$this->urlInterface = $urlInterface;
		parent::__construct($context);
	}
 

	public function execute()
	{
   
		$resultJson = $this->resultJsonFactory->create();
		$data =  $this->getRequest()->getPostValue();
		if($this->_customerSession->getCustomer()->getId()) {
			$quote_id = $this->saveQuoteRequest($data);
			// send mail here
			$customer = $this->_customerSession->getCustomer();
 			 $this->mailHelper->sendMail($quote_id,$customer,'QuoteRequest'.$quote_id.'.pdf');

			return $resultJson->setData(['response' => true,'message' => 'Quote Generated Successfully. Click <a class="quote_download" href="'.$this->urlInterface->getUrl('pdf/quote/request/quote_id/'.$quote_id).'">here</a> to download your quote']);
		}
		else{
			return $resultJson->setData(['response' => false,'message' => 'Some Error Occurs. Please try again later']);
		}
		
		
	}
	
	public function saveQuoteRequest($data){
		$data['customer_id'] = $this->_customerSession->getCustomer()->getId();
		$quote = $this->quote->setData($data)->save();
		$quote_id = $quote->getId();
		$data['quote_id'] = $quote_id;
		foreach($data['ck'] as $ck=>$productid) {
			$data['product_id'] = $productid;
 			$product = $this->product->load($productid);
			$data['sku'] = $product->getSku();
			$data['final_price'] = $product->getFinalPrice();
			$data['price'] = $product->getPrice();
 			$data['brand'] = $product->getAttributeText('brand');
			$mageproduct = $this->mproduct->load($productid,'mageproduct_id');
			$data['sellerid'] = $mageproduct->getData('seller_id');
 			$this->quoteItems->setData($data)->save();
		}
		return $quote_id;
	}
}