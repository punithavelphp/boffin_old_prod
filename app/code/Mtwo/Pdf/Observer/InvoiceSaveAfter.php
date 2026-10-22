<?php
namespace Mtwo\Pdf\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class InvoiceSaveAfter implements ObserverInterface
{
	protected $logger;

	public function __construct(LoggerInterface $logger,\Magento\Framework\App\RequestInterface $request)
	{
		$this->logger = $logger;
		$this->_request = $request;
	}

	public function execute(Observer $observer)
	{

		$invoice = $observer->getEvent()->getInvoice();
		$order = $invoice->getOrder();

		// Add your custom logic to update the invoice details here
		// For example, log some information
		$this->logger->info('Invoice created for order: ' . $order->getIncrementId());
		$postData = $this->_request->getPost();
		#$this->logger->info('JSON DATA: ' . json_encode($postData['invoice']));
		$invoice->setData('shipped_from',$postData['invoice']['shipped_from']);
		$invoice->getResource()->saveAttribute($invoice, 'shipped_from');
		
		if(!empty($postData['invoice']['invoice_number'])) {

 			// $postData['invoice']['invoice_date'] = $this->convertDate($postData['invoice']['invoice_date']);
			// Example: Add a custom attribute to the invoice

			$invoice->setData('increment_id',$postData['invoice']['invoice_number']);
			$invoice->setData('created_at',$postData['invoice']['invoice_date']);


			$invoice->getResource()->saveAttribute($invoice, 'po_number');
			$invoice->getResource()->saveAttribute($invoice, 'po_date');

			
            $invoice->getResource()->saveAttribute($invoice, 'increment_id');
			$invoice->getResource()->saveAttribute($invoice, 'created_at');

			 
		}
		if(!empty($postData['invoice']['po_number'])) {

 			// $postData['invoice']['invoice_date'] = $this->convertDate($postData['invoice']['invoice_date']);
			// Example: Add a custom attribute to the invoice

 
			$invoice->setData('po_number',$postData['invoice']['po_number']);
			$invoice->setData('po_date',$postData['invoice']['po_date']);
			
 			$invoice->getResource()->saveAttribute($invoice, 'po_number');
			$invoice->getResource()->saveAttribute($invoice, 'po_date');

			
 
			 
		}

		return $this;
	}

	public function convertDate($date)
	{
		$dateTime = \DateTime::createFromFormat('d-m-Y', $date);

		if ($dateTime === false) {
			throw new \Exception("Invalid date format: $date");
		}

		return $dateTime->format('Y-m-d');
	}

}