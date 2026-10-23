<?php
namespace Mtwo\Refrens\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;

class Combine extends Template
{
	public $invoiceRepository;

	protected $formKey;
	protected $invoiceCollectionFactory;

	public function __construct(Context $context, FormKey $formKey, InvoiceRepositoryInterface $invoiceRepository,InvoiceCollectionFactory $invoiceCollectionFactory,array $data = [])
	{
		$this->formKey = $formKey;
		$this->invoiceRepository = $invoiceRepository;
		$this->invoiceCollectionFactory = $invoiceCollectionFactory;
		parent::__construct($context, $data);
	}

	public function getFormKey()
	{
		return $this->formKey->getFormKey();
	}


	public function getInvoiceData($invoice_ids){
		$invoiceData = [];
		if (!empty($invoice_ids)) {
			$invoiceIds = explode(',', $invoice_ids);
			 
			foreach ($invoiceIds as $invoiceId) {
				$invoiceId = trim($invoiceId);
				try {
					$invoiceCollection = $this->invoiceCollectionFactory->create()
						->addAttributeToFilter('increment_id', $invoiceId);

					if ($invoiceCollection->getSize()) {
						$invoice = $invoiceCollection->getFirstItem();
						$invoiceData[] = $invoice;
					} else {
						//$this->messageManager->addErrorMessage(__('Invoice Increment ID %1 not found.', $invoiceIncrementId));
					}

				} catch (\Exception $e) {
					//echo $e->getMessage();
				}
			}	
		}
		return $invoiceData;
	}
} 