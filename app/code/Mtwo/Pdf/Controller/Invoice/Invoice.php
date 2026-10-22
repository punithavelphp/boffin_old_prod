<?php

namespace Mtwo\Pdf\Controller\Invoice;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\SellerFactory as MpSellerModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mtwo\Pdf\Controller\Mypdf;
use TCPDF;
class Invoice extends Action
{
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
		, OrderRepositoryInterface $orderRepository
		, \Magento\Framework\Registry $coreRegistry
		, \Magento\Customer\Model\Session $customerSession
		, \Webkul\Marketplace\Helper\Orders $orderHelper
		, HelperData $helper
		,\Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
		, MpSellerModel $mpSellerModel
		, DateTime $dateTime
		,\Psr\Log\LoggerInterface  $logger

	) {
		$this->_resultPageFactory = $resultPageFactory;
		$this->helper = $helper;
		$this->_coreRegistry = $coreRegistry;
		$this->_invoiceRepository = $invoiceRepository;
		$this->_orderRepository = $orderRepository;
		$this->_customerSession = $customerSession;
		$this->orderHelper = $orderHelper;
		$this->helper = $helper;
		$this->date = $dateTime; 
		$this->mpSellerModel = $mpSellerModel;
		$this->logger = $logger;
		parent::__construct($context);
	}

	/**
     * Marketplace Landing page.
     *
     * @return \Magento\Framework\View\Result\Page
     */


	public function _initSellerInvoice($order)
	{
		try {
			$orderId = $order->getId();
			$tracking = $this->orderHelper->getOrderinfo($orderId);
			$invoice_tracking = 0;
			if ($tracking) {
				$invoiceId = $tracking->getInvoiceId();
$invoiceIdArray = explode(',',$invoiceId);
				foreach($invoiceIdArray as $invoiceId){
if($invoiceId >0){
					$invoice_tracking = $invoiceId	;
}
					
				}
				$paramInvoiceId = $this->getRequest()->getParam('invoice_id');
				if($paramInvoiceId) {
					$invoice_tracking = $paramInvoiceId	;
				} 
				
				if ($invoice_tracking) {
					$invoice = $this->_invoiceRepository->get($invoice_tracking);
					if (!$invoice) {
						return false;
					}
				} else {
					$this->messageManager->addError(__('You are not authorize to view this invoice.'));
					$this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
					return false;
				}
			} else {
				$this->messageManager->addError(__('You are not authorize to view this invoice.'));
				$this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

				return false;
			}
		} catch (NoSuchEntityException $e) {
			$this->helper->logDataInLogger(
				"Controller_Order execute : ".$e->getMessage()
			);

			return false;
		} catch (InputException $e) {
			$this->helper->logDataInLogger(
				"Controller_Order execute : ".$e->getMessage()
			);
			return false;
		}
		$this->_coreRegistry->register('sales_order', $order);
		$this->_coreRegistry->register('current_order', $order);

		return $invoice;
	}



	public function execute()
	{

		$helper = $this->helper;
		$isPartner = $helper->isSeller();
		if ($isPartner == 1) {
			$sellerId = $this->_customerSession->getCustomerId();
			$orderId = $this->getRequest()->getParam('order');
			$order = $this->_orderRepository->get($orderId);
			$invoice = $this->_initSellerInvoice($order);
			
			 
			
			if($invoice){

				$tcpdf = new Mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				 
				
				$layout = $this->_view->getLayout();
				$tcpdf->SetAutoPageBreak(TRUE, 85);
				$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
				// $tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
				$tcpdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, '', '', array(
					0,
					0,
					0
				), array(
					255,
					255,
					255
				));
				$tcpdf->SetTitle('Invoice - '.$invoice->getIncrementId() );
				$tcpdf->SetMargins(20, 10, 20, true);

				$tcpdf->SetFont('helvetica', '', 11);
				 $totalPages = count($invoice->getAllItems());
 				$totalPages = $totalPages ;
				if($totalPages > 12 && $totalPages <= 28){
					$totalPages = 2;
				}
				else if($totalPages > 12){
					$totalPages = $totalPages / 12;
					$totalPages = ceil($totalPages);
				}else if($totalPages > 7){
					$totalPages = $totalPages / 7;
					$totalPages = ceil($totalPages);
				}else{
					$totalPages = 1;
				}
				//      $totalPages = $this->getNumPages(); // Store the total pages
		 		 $tcpdf->setPageCount($totalPages);
 				$tcpdf->setMultipleCopy(true);
				$html= $layout
					->createBlock('Mtwo\Pdf\Block\Invoice')
					->setTemplate('Mtwo_Pdf::invoice.phtml')
					->setIsSeller($sellerId)
					->setOrder($order)
					->setInvoice($invoice)
					->setHeadingText('Tax Invoice')
					->toHtml();

				 if(isset($_GET['echo'])) {
					echo $html;
					die;
				 }

				// set some language dependent data:

				$lg = Array();

				$lg['a_meta_charset'] = 'UTF-8';


				$tcpdf->setLanguageArray($lg);


				// set font

				//dejavusans & freesans For Indian Rupees symbol

				$tcpdf->SetFont('freesans', '', 12);

				// remove default header/footer

				//$tcpdf->setPrintHeader(false);

				$tcpdf->setPrintFooter(true);


				$tcpdf->AddPage();


				$tcpdf->writeHTML($html, true, false, true, false, '');

 /*
 				
				$tcpdf->AddPage();
			 $totalPages = count($invoice->getAllItems());
 				
		 

$html= $layout
					->createBlock('Mtwo\Pdf\Block\Invoice')
					->setTemplate('Mtwo_Pdf::invoice.phtml')
					->setIsSeller($sellerId)
					->setOrder($order)
					->setInvoice($invoice)
					->setHeadingText('Transporter Copy')
					->toHtml();
				$tcpdf->writeHTML($html, true, false, true, false, '');

 */
			 	$tcpdf->lastPage();
				
				

				//$tcpdf->Output('report_per_route.pdf', 'I');

				//$this->logger->debug('report_per_route');
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

				$fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
				$mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();





				$baseurl = $mediaPath;
				$filename = $baseurl .'/export/'. 'invoice_'. time().'.pdf';


				$tcpdf->Output('invoice_'.$invoice->getIncrementId().'.pdf', 'D');
				exit;

			} 
		}


		$resultPage = $this->_resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Sample PDF'));

		return $resultPage;
	}
}


