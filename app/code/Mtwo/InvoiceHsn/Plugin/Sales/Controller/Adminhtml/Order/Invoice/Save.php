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

namespace Mtwo\InvoiceHsn\Plugin\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Sales\Controller\Adminhtml\Order\Invoice\Save as InvoiceSave;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Mtwo\InvoiceHsn\Helper\Data as HsnHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 *
 * @package Mtwo\InvoiceHsn\Plugin\Sales\Controller\Adminhtml\Order\Invoice
 */
class Save
{
    /**
     * @var HsnHelper
     */
    protected $hsnHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Save constructor.
     *
     * @param HsnHelper $hsnHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        HsnHelper $hsnHelper,
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        UrlInterface $urlBuilder
    ) {
        $this->hsnHelper = $hsnHelper;
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Validate HSN codes before invoice creation in admin
     *
     * @param InvoiceSave $subject
     * @return array|null
     */
    public function beforeExecute(InvoiceSave $subject)
    {
        try {
            $orderId = $subject->getRequest()->getParam('order_id');
            
            if (!$orderId) {
                return null;
            }

            $order = $this->orderRepository->get($orderId);
            $itemsWithoutHsn = $this->hsnHelper->getOrderItemsWithoutHsn($order);

            if (!empty($itemsWithoutHsn)) {
                $itemLinks = [];
                foreach ($itemsWithoutHsn as $item) {
                    $editUrl = $this->urlBuilder->getUrl('catalog/product/edit', [
                        'id' => $item['product_id'],
                        '_fragment' => 'product_form_tab_attributes'
                    ]);
                    
                    $itemLinks[] = sprintf(
                        '<a href="%s" target="_blank" style="color: #007bdb; text-decoration: underline;">%s (SKU: %s)</a>',
                        $editUrl,
                        $item['name'],
                        $item['sku']
                    );
                }

                $errorMessage = __(
                    'Cannot create invoice. The following products are missing HSN codes: %1. ' .
                    'Click on product links to open and add HSN codes.',
                    implode(', ', $itemLinks)
                );

                // Use addComplexErrorMessage to allow HTML in the message
                $this->messageManager->addComplexErrorMessage(
                    'addErrorMessage',
                    [
                        'message' => $errorMessage
                    ]
                );
                
                // Create simple array for logging
                $itemNames = [];
                foreach ($itemsWithoutHsn as $item) {
                    $itemNames[] = sprintf('%s (SKU: %s)', $item['name'], $item['sku']);
                }
                
                $this->logger->warning('Admin invoice creation blocked due to missing HSN codes', [
                    'order_id' => $orderId,
                    'items_without_hsn' => $itemNames
                ]);

                // Return empty array to prevent execution
                return [];
            }

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return [];
        } catch (\Exception $e) {
            $this->logger->error('Error validating HSN codes for invoice: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(
                __('An error occurred while validating HSN codes. Please try again.')
            );
            return [];
        }

        return null;
    }
}