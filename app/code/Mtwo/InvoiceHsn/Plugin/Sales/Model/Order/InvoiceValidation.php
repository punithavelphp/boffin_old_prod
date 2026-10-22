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

namespace Mtwo\InvoiceHsn\Plugin\Sales\Model\Order;

use Magento\Sales\Model\Order\Invoice;
use Mtwo\InvoiceHsn\Helper\Data as HsnHelper;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class InvoiceValidation
 *
 * @package Mtwo\InvoiceHsn\Plugin\Sales\Model\Order
 */
class InvoiceValidation
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
     * InvoiceValidation constructor.
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
     * Validate HSN codes before invoice registration
     *
     * @param Invoice $subject
     * @throws LocalizedException
     */
    public function beforeRegister(Invoice $subject)
    {
        $this->validateInvoiceHsnCodes($subject);
    }

    /**
     * Validate HSN codes before invoice save
     *
     * @param Invoice $subject
     * @throws LocalizedException
     */
    public function beforeSave(Invoice $subject)
    {
        // Only validate if the invoice is being created (not updated)
        if (!$subject->getId()) {
            $this->validateInvoiceHsnCodes($subject);
        }
    }

    /**
     * Validate that all invoice items have HSN codes
     *
     * @param Invoice $invoice
     * @throws LocalizedException
     */
    protected function validateInvoiceHsnCodes(Invoice $invoice)
    {
        $itemsWithoutHsn = [];
        $items = $invoice->getAllItems();

        foreach ($items as $item) {
            // Skip bundle and configurable child items
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }

            $productId = $item->getProductId();
            $hsnCode = $this->hsnHelper->getProductHsnCode($productId);

            if (empty($hsnCode)) {
                $productName = $item->getName();
                $productSku = $item->getSku();
                $itemsWithoutHsn[] = sprintf(
                    '%s (SKU: %s)',
                    $productName,
                    $productSku
                );
            }
        }

        if (!empty($itemsWithoutHsn)) {
            $errorMessage = __(
                'Cannot create invoice. The following products are missing HSN codes: %1. ' .
                'Please assign HSN codes to these products before creating the invoice.',
                implode(', ', $itemsWithoutHsn)
            );
            
            $this->logger->warning('Invoice creation blocked due to missing HSN codes', [
                'invoice_id' => $invoice->getId(),
                'order_id' => $invoice->getOrderId(),
                'items_without_hsn' => $itemsWithoutHsn
            ]);

            throw new LocalizedException($errorMessage);
        }
    }
}