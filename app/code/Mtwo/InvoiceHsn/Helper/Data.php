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

namespace Mtwo\InvoiceHsn\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Data
 *
 * @package Mtwo\InvoiceHsn\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Get HSN code for a product
     *
     * @param int $productId
     * @return string|null
     */
    public function getProductHsnCode($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
            $hsnCode = $product->getCustomAttribute('hsncode');
            
            if ($hsnCode && $hsnCode->getValue()) {
                return $hsnCode->getValue();
            }
            
            return null;
        } catch (NoSuchEntityException $e) {
            $this->logger->error('Product not found: ' . $productId . ' - ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->logger->error('Error getting HSN code for product ' . $productId . ': ' . $e->getMessage());
            return null;
        }
    }

    // HSN code save functionality removed - display and validation only

    /**
     * Check if HSN code attribute exists for products
     *
     * @return bool
     */
    public function isHsnCodeAttributeExists()
    {
        try {
            $this->attributeRepository->get('catalog_product', 'hsncode');
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Get HSN code from GST number (if needed for mapping)
     *
     * @param string $gstNumber
     * @return string|null
     */
    public function getHsnCodeFromGst($gstNumber)
    {
        // This method can be extended to fetch HSN code from GST number
        // if there's a mapping table or external API
        // For now, return null as basic implementation
        return null;
    }

    /**
     * Validate HSN code format
     *
     * @param string $hsnCode
     * @return bool
     */
    public function validateHsnCode($hsnCode)
    {
        // HSN codes are typically 4, 6, or 8 digits
        return preg_match('/^\d{4,8}$/', $hsnCode);
    }

    // Method to get products without HSN code removed - not needed for validation-only functionality

    /**
     * Get HSN code display format
     *
     * @param string $hsnCode
     * @return string
     */
    public function formatHsnCodeDisplay($hsnCode)
    {
        if (!$hsnCode) {
            return __('Not Set');
        }
        
        return strtoupper($hsnCode);
    }

    /**
     * Get invoice items without HSN codes
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return array
     */
    public function getInvoiceItemsWithoutHsn($invoice)
    {
        $itemsWithoutHsn = [];
        $items = $invoice->getAllItems();

        foreach ($items as $item) {
            // Skip bundle and configurable child items
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }

            $productId = $item->getProductId();
            $hsnCode = $this->getProductHsnCode($productId);

            if (empty($hsnCode)) {
                $itemsWithoutHsn[] = [
                    'product_id' => $productId,
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'item_id' => $item->getId()
                ];
            }
        }

        return $itemsWithoutHsn;
    }

    /**
     * Check if all order items have HSN codes
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function validateOrderItemsHaveHsnCodes($order)
    {
        foreach ($order->getAllVisibleItems() as $item) {
            $productId = $item->getProductId();
            $hsnCode = $this->getProductHsnCode($productId);
            
            if (empty($hsnCode)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get order items without HSN codes
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getOrderItemsWithoutHsn($order)
    {
        $itemsWithoutHsn = [];
         
        foreach ($order->getItems() as $item) {
            $productId = $item->getProductId();
            $hsnCode = $this->getProductHsnCode($productId);
             
            if (empty($hsnCode)) {
                $itemsWithoutHsn[] = [
                    'product_id' => $productId,
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'item_id' => $item->getId()
                ];
            }
        }
        
        return $itemsWithoutHsn;
    }
}