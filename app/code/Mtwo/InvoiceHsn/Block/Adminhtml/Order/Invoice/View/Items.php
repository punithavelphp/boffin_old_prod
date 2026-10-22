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

namespace Mtwo\InvoiceHsn\Block\Adminhtml\Order\Invoice\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mtwo\InvoiceHsn\Helper\Data as HsnHelper;
use Magento\Sales\Api\Data\InvoiceInterface;

/**
 * Class Items
 *
 * @package Mtwo\InvoiceHsn\Block\Adminhtml\Order\Invoice\View
 */
class Items extends Template
{
    /**
     * @var HsnHelper
     */
    protected $hsnHelper;

    /**
     * @var InvoiceInterface
     */
    protected $invoice;

    /**
     * Items constructor.
     *
     * @param Context $context
     * @param HsnHelper $hsnHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        HsnHelper $hsnHelper,
        array $data = []
    ) {
        $this->hsnHelper = $hsnHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get HSN code for product
     *
     * @param int $productId
     * @return string
     */
    public function getHsnCode($productId)
    {
        return $this->hsnHelper->getProductHsnCode($productId);
    }

    /**
     * Format HSN code for display
     *
     * @param string $hsnCode
     * @return string
     */
    public function formatHsnCode($hsnCode)
    {
        return $this->hsnHelper->formatHsnCodeDisplay($hsnCode);
    }

    // AJAX save URL method removed - display and validation only

    /**
     * Set invoice
     *
     * @param InvoiceInterface $invoice
     * @return $this
     */
    public function setInvoice(InvoiceInterface $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * Get invoice
     *
     * @return InvoiceInterface|null
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Check if HSN code attribute exists
     *
     * @return bool
     */
    public function isHsnCodeAttributeExists()
    {
        return $this->hsnHelper->isHsnCodeAttributeExists();
    }
}