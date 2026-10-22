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

namespace Mtwo\InvoiceHsn\Block\Adminhtml\Order\Invoice;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mtwo\InvoiceHsn\Helper\Data as HsnHelper;
use Magento\Framework\Registry;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ValidationScript
 *
 * @package Mtwo\InvoiceHsn\Block\Adminhtml\Order\Invoice
 */
class ValidationScript extends Template
{
    /**
     * @var HsnHelper
     */
    protected $hsnHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * ValidationScript constructor.
     *
     * @param Context $context
     * @param HsnHelper $hsnHelper
     * @param Registry $registry
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        HsnHelper $hsnHelper,
        Registry $registry,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->hsnHelper = $hsnHelper;
        $this->registry = $registry;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get current order
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        // Try different registry keys used in different invoice pages
        $order = $this->registry->registry('current_order');
        if (!$order) {
            $order = $this->registry->registry('order');
        }
        if (!$order) {
            $invoice = $this->registry->registry('current_invoice');
            if ($invoice) {
                $order = $invoice->getOrder();
            }
        }
        if (!$order) {
            // Try getting order ID from request parameters
            $orderId = $this->getRequest()->getParam('order_id');
            if ($orderId) {
                try {
                    $order = $this->orderRepository->get($orderId);
                } catch (\Exception $e) {
                    // Log error but don't break - return null order
                }
            }
        }
        return $order;
    }

    /**
     * Get order items without HSN codes
     *
     * @return array
     */
    public function getItemsWithoutHsn()
    {
        $order = $this->getOrder();
        if (!$order) {
            return [];
        }

        return $this->hsnHelper->getOrderItemsWithoutHsn($order);
    }

    // Validation URL method removed - using server-side validation only
}