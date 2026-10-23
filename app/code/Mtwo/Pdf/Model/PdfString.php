<?php

namespace Mtwo\Pdf\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\SellerFactory as MpSellerModel;
use Magento\Framework\Stdlib\DateTime\DateTime;

use TCPDF;
class PdfString  
{
	public $helper;
	public $_coreRegistry;
	public $_invoiceRepository;
	public $_orderRepository;
	public $_customerSession;
	public $orderHelper;
	public $date;
	public $mpSellerModel;
	public $logger;
	public $layoutFactory;

	/**
     * @var PageFactory
     */
	protected $_resultPageFactory;


	/**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
	public function __construct(
 		  OrderRepositoryInterface $orderRepository
		, \Magento\Framework\Registry $coreRegistry
		, \Magento\Customer\Model\Session $customerSession
		, \Webkul\Marketplace\Helper\Orders $orderHelper
		, HelperData $helper
		,\Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
		, MpSellerModel $mpSellerModel
		, DateTime $dateTime
		,\Psr\Log\LoggerInterface  $logger
		,\Magento\Framework\View\LayoutFactory $layoutFactory

	) {
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
		 $this->layoutFactory = $layoutFactory;
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
			$tracking = $this->orderHelper->getCustomerOrderinfo($orderId);
			if ($tracking) {
				if ($tracking->getInvoiceId()) {
					$invoice = $this->_invoiceRepository->get($tracking->getInvoiceId());
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


	public function getPdfAsString($invoiceId){
		$invoice = '';
		$helper = $this->helper;
	//	$invoiceId = $this->getRequest()->getParam('invoice_id');
	//	$orderId = $this->getRequest()->getParam('order_id');
		if($invoiceId && $invoiceId != ''){
			$invoice =	$this->_invoiceRepository->get($invoiceId);
			$orderId = $invoice->getOrder()->getId();
		}

	 	$order = $this->_orderRepository->get($orderId);
		if($invoice || $order){

			if($invoice){

			}else{
				$invoice = $this->_initSellerInvoice($order);
			}
			if($invoice){
				$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$layout =     $this->layoutFactory->create();
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
				$tcpdf->SetTitle('Invoice - '.$invoice->getIncrementId() );
				$tcpdf->SetMargins(20, 10, 20, true);

				$tcpdf->SetFont('helvetica', '', 11);
				$lg = Array();

				$lg['a_meta_charset'] = 'UTF-8';


				$tcpdf->setLanguageArray($lg);


				// set font

				//dejavusans & freesans For Indian Rupees symbol

				$tcpdf->SetFont('freesans', '', 12);

				// remove default header/footer

				//$tcpdf->setPrintHeader(false);

				$tcpdf->setPrintFooter(false);
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$sellerIdArray = array();
				$sellerProductIdArray = array();
				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				$connection = $resource->getConnection();
				$tableName = $resource->getTableName('marketplace_saleslist');
				$query = "Select * FROM " . $tableName."  where order_id = '".$orderId."'"; 
				$result = $connection->fetchAll($query); 
				foreach($result as $res){
					$sellerIdArray[$res['seller_id']] = $res['seller_id'];
					$sellerProductIdArray[$res['seller_id']][] = $res['mageproduct_id'];
				}
				$html_write = '';
				if(count($sellerIdArray) > 0){
					
					
				}else{
					
					$webkulObserver = $objectManager->get('Webkul\Marketplace\Observer\SalesOrderSuccessObserver');
					
					foreach($order->getAllItems() as $item){
 						$sellerId = $webkulObserver->getSellerIdPerProduct($item);
						if($sellerId != 0){
							$sellerIdArray[$sellerId][] = $item->getProductId();
							$sellerProductIdArray[$sellerId][] = $item->getProductId();
 						}
					}
					 
				}
				if(count($sellerIdArray) > 0){
 					$this->logger->debug('$sellerIdArray-----1----->'.json_encode($sellerIdArray));
				}else{
					return 'empty';	
				}
				 					$this->logger->debug('$sellerIdArray----2------>'.json_encode($sellerIdArray));

				foreach($sellerIdArray as $sellerId=>$item) {
				 
 					$html= $layout
						->createBlock('Mtwo\Pdf\Block\Invoice')
						->setTemplate('Mtwo_Pdf::invoice.phtml')
 						->setIsSeller($sellerId)
						->setOrder($order)
 						->setSellerProductArray($sellerProductIdArray[$sellerId])
						->setBase(1)
						->setInvoice($invoice)
						->toHtml();
 

					$tcpdf->AddPage();

					$tcpdf->writeHTML($html, true, false, true, false, '');
					$html_write .= $html;
				}

				if(isset($_GET['echo'])) {
					echo $html_write;
					die;
				} 
				$tcpdf->lastPage();

				//$tcpdf->Output('report_per_route.pdf', 'I');

				//$this->logger->debug('report_per_route');

				$fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
				$mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();





				$baseurl = $mediaPath;
				$filename = $baseurl .'/export/'. 'Sample_pdf'. time().'.pdf';


				return $tcpdf->Output('invoice_'.$invoice->getIncrementId().'.pdf', 'S');

			} 
			return $file;
		}
	}

	public function execute()
	{

		$helper = $this->helper;
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$orderId = $this->getRequest()->getParam('order_id');
		if($invoiceId && $invoiceId != ''){
			$invoice =	$this->_invoiceRepository->get($invoiceId);
			$orderId = $invoice->getOrder()->getId();
		}

		$order = $this->_orderRepository->get($orderId);
		if($invoice || $order){

			if($invoice){

			}else{
				$invoice = $this->_initSellerInvoice($order);
			}
			if($invoice){
				$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$layout = $this->_view->getLayout();
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
				$tcpdf->SetTitle('Invoice - '.$invoice->getIncrementId() );
				$tcpdf->SetMargins(20, 10, 20, true);

				$tcpdf->SetFont('helvetica', '', 11);
				$lg = Array();

				$lg['a_meta_charset'] = 'UTF-8';


				$tcpdf->setLanguageArray($lg);


				// set font

				//dejavusans & freesans For Indian Rupees symbol

				$tcpdf->SetFont('freesans', '', 12);

				// remove default header/footer

				//$tcpdf->setPrintHeader(false);

				$tcpdf->setPrintFooter(false);
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				$connection = $resource->getConnection();
				$tableName = $resource->getTableName('marketplace_saleslist');
				$query = "Select * FROM " . $tableName."  where order_id = '".$orderId."'"; 
				$result = $connection->fetchAll($query); 
				foreach($result as $res){
					$sellerIdArray[$res['seller_id']] = $res['seller_id'];
				}
				$html_write = '';
				foreach($sellerIdArray as $sellerId) {
					$html= $layout
						->createBlock('Mtwo\Pdf\Block\Invoice')
						->setTemplate('Mtwo_Pdf::invoice.phtml')
						->setIsSeller($sellerId)
						->setOrder($order)
						->setInvoice($invoice)
						->toHtml();


					// set some language dependent data:


					$tcpdf->AddPage();

					$tcpdf->writeHTML($html, true, false, true, false, '');
					$html_write .= $html;
				}

				if(isset($_GET['echo'])) {
					echo $html_write;
					die;
				}
				$tcpdf->lastPage();

				//$tcpdf->Output('report_per_route.pdf', 'I');

				//$this->logger->debug('report_per_route');

				$fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
				$mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();





				$baseurl = $mediaPath;
				$filename = $baseurl .'/export/'. 'Sample_pdf'. time().'.pdf';


				$tcpdf->Output('invoice_'.$invoice->getIncrementId().'.pdf', 'D');
				exit;

			} 
			$resultPage = $this->_resultPageFactory->create();
			$resultPage->getConfig()->getTitle()->set(__('Sample PDF'));
			return $resultPage;
		}
	}
}


