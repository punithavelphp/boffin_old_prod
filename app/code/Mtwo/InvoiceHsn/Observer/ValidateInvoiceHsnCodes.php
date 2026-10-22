<?php
/**
 * Mtwo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mtwo.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mtwo.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mtwo
 * @package   Mtwo_InvoiceHsn
 * @copyright Copyright (c) Mtwo (https://www.mtwo.com/)
 * @license   https://www.mtwo.com/LICENSE.txt
 */

namespace Mtwo\InvoiceHsn\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Mtwo\InvoiceHsn\Helper\Data as HsnHelper;
use Magento\Sales\Model\Order\Invoice;
use Psr\Log\LoggerInterface;

/**
 * Class ValidateInvoiceHsnCodes
 *
 * @package Mtwo\InvoiceHsn\Observer
 */
class ValidateInvoiceHsnCodes implements ObserverInterface
{
    /**
     * @var HsnHelper
     */
    protected $hsnHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ValidateInvoiceHsnCodes constructor.
     *
     * @param HsnHelper $hsnHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        HsnHelper $hsnHelper,
        LoggerInterface $logger
    ) {
        $this->hsnHelper = $hsnHelper;
        $this->logger = $logger;
    }

    /**
     * Validate HSN codes before invoice operations
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        
        if (!$invoice) {
            return;
        }

        // Only validate for new invoices
        if ($invoice->getId()) {
            return;
        }

        $this->validateHsnCodes($invoice);
    }

    /**
     * Validate that all invoice items have HSN codes
     *
     * @param Invoice $invoice
     * @throws LocalizedException
     */
    protected function validateHsnCodes(Invoice $invoice)
    {
        $missingHsnItems = $this->hsnHelper->getInvoiceItemsWithoutHsn($invoice);

        if (!empty($missingHsnItems)) {
            $itemList = [];
            foreach ($missingHsnItems as $item) {
                $itemList[] = sprintf(
                    '%s (SKU: %s)',
                    $item['name'],
                    $item['sku']
                );
            }

            $errorMessage = __(
                'Invoice cannot be created. The following products are missing HSN codes: %1. ' .
                'Please assign HSN codes to these products before invoicing.',
                implode(', ', $itemList)
            );

            $this->logger->warning('Invoice creation blocked - missing HSN codes', [
                'order_id' => $invoice->getOrderId(),
                'missing_hsn_items' => $itemList
            ]);

            throw new LocalizedException($errorMessage);
        }
    }
}