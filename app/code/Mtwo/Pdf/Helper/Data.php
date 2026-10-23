<?php 
declare(strict_types = 1);
namespace Mtwo\Pdf\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;

use TCPDF;

class Data extends AbstractHelper
{
	public $fileSystem;
	public $_blockFactory;
	public $invoiceBlock;
	public $storeManager;
	public $customerFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context
								,\Magento\Framework\Filesystem $fileSystem
 								,\Magento\Framework\View\Element\BlockFactory $blockFactory
								,\Mtwo\Pdf\Block\Invoice $invoiceBlock
								,StoreManagerInterface $storeManager
								,CustomerFactory $customerFactory
							   )
    {
		$this->fileSystem = $fileSystem;
		$this->_blockFactory = $blockFactory;
		$this->invoiceBlock = $invoiceBlock;
		$this->storeManager = $storeManager;
		$this->customerFactory = $customerFactory;
         parent::__construct($context);
		
    }

	
		public function generateCartQuotePdf($quoteData,$OUTPUT = 'D'){
			$quoteId = $quoteData->getId();
			
		$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$layout = $this->_blockFactory;
		$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$tcpdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, '', '', array(
			0,
			0,
			0
		), array(
			255,
			255,
			255
		));
		$tcpdf->SetTitle('Quote Request - '.$quoteId);
		$tcpdf->SetMargins(20, 10, 20, true);

		$tcpdf->SetFont('helvetica', '', 11);
		$lg = Array();

		$lg['a_meta_charset'] = 'UTF-8';


		$tcpdf->setLanguageArray($lg);
 

		$tcpdf->SetFont('freesans', '', 12);

 
		$tcpdf->setPrintFooter(false);


	 
		
  
 $customerId = $quoteData->getData('customer_id');
$quoteItems = $this->invoiceBlock->getQuoteDetailItems($quoteId);
		$customerModel = $this->getCustomer($customerId);
		$billing_ID = $customerModel->getDefaultBilling();
		$shipping_ID = $customerModel->getDefaultShipping();
		$customerAddress = $this->getCustomerAddress($customerModel);
		$customerBillingAddress = $customerAddress[$billing_ID];
			$customerShippingAddress = $customerAddress[$shipping_ID];
	 $html_write = '';
		 
			$tcpdf->AddPage();
			$html =  $layout
						->createBlock('Mtwo\Pdf\Block\Invoice')
						->setTemplate('Mtwo_Pdf::quote_cart.phtml')
						->setQuoteId($quoteId)
						->setQuoteData($quoteData)
						->setQuoteItems($quoteItems)
						->setCustomer($customerModel)
						->setCustomerShippingAddress($customerShippingAddress)
						->setCustomerBIllingAddress($customerBillingAddress)
 						->toHtml();
			$tcpdf->writeHTML($html, true, false, true, false, '');
			
			$html_write .= $html;
		 

		if(isset($_GET['echo'])) {
			echo $html_write;
			die;
		}
		$tcpdf->lastPage();


		$mediaPath = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();

		$baseurl = $mediaPath;
		$filename = $baseurl .'/export/'. 'quote_cart_request_'.date('YmdHis').'.pdf';

		return $tcpdf->Output('quote_cart_request_'.$quoteId.'_'.date('YmdHis').'.pdf', $OUTPUT);	
	}
	
	public function generateQuotePdf($quoteId,$OUTPUT = 'D'){
		
		$quoteData  = $this->invoiceBlock->getQuoteDetails($quoteId);
		 
		if($quoteData->getIsQuoteCart() == 1){
			return $this->generateCartQuotePdf($quoteData,$OUTPUT = 'D');
			
		}
		
		$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$layout = $this->_blockFactory;
		$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$tcpdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, '', '', array(
			0,
			0,
			0
		), array(
			255,
			255,
			255
		));
		$tcpdf->SetTitle('Quote Request - '.$quoteId);
		$tcpdf->SetMargins(20, 10, 20, true);

		$tcpdf->SetFont('helvetica', '', 11);
		$lg = Array();

		$lg['a_meta_charset'] = 'UTF-8';


		$tcpdf->setLanguageArray($lg);
 

		$tcpdf->SetFont('freesans', '', 12);

 
		$tcpdf->setPrintFooter(false);


	 
		
 
 $customerId = $quoteData->getData('customer_id');
$quoteItems = $this->invoiceBlock->getQuoteDetailItems($quoteId);
		$customerModel = $this->getCustomer($customerId);
		$billing_ID = $customerModel->getDefaultBilling();
		$shipping_ID = $customerModel->getDefaultShipping();
		$customerAddress = $this->getCustomerAddress($customerModel);
		$customerBillingAddress = $customerAddress[$billing_ID];
			$customerShippingAddress = $customerAddress[$shipping_ID];
	 $html_write = '';
		foreach($quoteItems as $quoteItem) {
			$tcpdf->AddPage();
			$html =  $layout
						->createBlock('Mtwo\Pdf\Block\Invoice')
						->setTemplate('Mtwo_Pdf::quote.phtml')
						->setQuoteId($quoteId)
						->setQuoteData($quoteData)
						->setCustomer($customerModel)
						->setCustomerShippingAddress($customerShippingAddress)
						->setCustomerBIllingAddress($customerBillingAddress)
						->setQuoteItem($quoteItem)
						->toHtml();
			$tcpdf->writeHTML($html, true, false, true, false, '');
			
			$html_write .= $html;
		}

		if(isset($_GET['echo'])) {
			echo $html_write;
			die;
		}
		$tcpdf->lastPage();


		$mediaPath = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();

		$baseurl = $mediaPath;
		$filename = $baseurl .'/export/'. 'quote_request_'.date('YMDHIS').'.pdf';

		return $tcpdf->Output('quote_request_'.$quoteId.'_'.date('YmdHis').'.pdf', $OUTPUT);	
	}
	public function getCustomer($customerId){
		$customer = $this->customerFactory->create();
		$websiteId = $this->storeManager->getStore()->getWebsiteId();
		$customer->setWebsiteId($websiteId);
		$customerModel = $customer->load($customerId);
		return $customerModel;
	}
	
	 public function getCustomerAddress($customerModel)
	{
		$customerAddressData = [];
		$customerAddress = [];
		if ($customerModel->getAddresses() != null) {
			foreach ($customerModel->getAddresses() as $address) {
				$customerAddress[] = $address;
			}
		}

		if ($customerAddress != null) {
			foreach ($customerAddress as $customerAddres) {
				$customerAddressD['entity_id'] = $customerAddres['entity_id'];
				$customerAddressD['street'] = $customerAddres['street'];
				$customerAddressD['city']  = $customerAddres['city'];
				$customerAddressD['region']  = $customerAddres['region'];
				$customerAddressD['region_id']  = $customerAddres['region_id'];
				$customerAddressD['country_id']  = $customerAddres['country_id'];
				$customerAddressD['postcode']  = $customerAddres['postcode'];
 				$customerAddressData[$customerAddres['entity_id']] = $customerAddres;
			}
		}
		return $customerAddressData;
	}
		
		
}