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

namespace Mtwo\InvoiceHsn\Plugin\Sales\Block\Adminhtml\Order\Invoice\View;

use Magento\Sales\Block\Adminhtml\Order\Invoice\View\Items as InvoiceItems;
use Mtwo\InvoiceHsn\Helper\Data as HsnHelper;
use Magento\Framework\UrlInterface;

/**
 * Class Items
 *
 * @package Mtwo\InvoiceHsn\Plugin\Sales\Block\Adminhtml\Order\Invoice\View
 */
class Items
{
    /**
     * @var HsnHelper
     */
    protected $hsnHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Items constructor.
     *
     * @param HsnHelper $hsnHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        HsnHelper $hsnHelper,
        UrlInterface $urlBuilder
    ) {
        $this->hsnHelper = $hsnHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Add HSN code column to invoice items
     *
     * @param InvoiceItems $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(InvoiceItems $subject, $result)
    {
        // Only modify if we have invoice items
        $invoice = $subject->getInvoice();
        if (!$invoice || !$invoice->getAllItems()) {
            return $result;
        }

        // Check if we already modified this HTML (prevent double processing)
        if (strpos($result, 'col-hsn-code') !== false) {
            return $result;
        }

        // Try multiple patterns to find the table structure
        $headerPatterns = [
            '/(<th[^>]*class="[^"]*col-price[^"]*"[^>]*>.*?<\/th>)/i',
            '/(<th[^>]*>.*?Price.*?<\/th>)/i',
            '/(<th[^>]*>.*?Total.*?<\/th>)/i'
        ];

        $rowPatterns = [
            '/(<tr[^>]*>.*?<td[^>]*class="[^"]*col-price[^"]*"[^>]*>.*?<\/td>)/is',
            '/(<tr[^>]*>.*?<td[^>]*>.*?\$.*?<\/td>)/is'
        ];

        // Add HSN header
        $hsnHeader = '<th class="col-hsn-code">' . __('HSN Code') . '</th>';
        foreach ($headerPatterns as $pattern) {
            if (preg_match($pattern, $result)) {
                $result = preg_replace($pattern, '$1' . $hsnHeader, $result, 1);
                break;
            }
        }

        // Add HSN cells for each item
        $itemIndex = 0;
        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            
            $productId = $item->getProductId();
            $hsnCode = $this->hsnHelper->getProductHsnCode($productId);
            $hsnDisplay = $this->hsnHelper->formatHsnCodeDisplay($hsnCode);
            
            // Create HSN code cell
            $hsnCell = $this->createHsnCell($productId, $hsnCode, $hsnDisplay);
            
            // Try to add the cell to the corresponding row
            foreach ($rowPatterns as $pattern) {
                if (preg_match($pattern, $result)) {
                    $result = preg_replace($pattern, '$1' . $hsnCell, $result, 1);
                    break 2; // Break out of both loops
                }
            }
            $itemIndex++;
        }

        // Add JavaScript and CSS
        $result .= $this->getHsnCodeScripts();

        return $result;
    }

    /**
     * Create HSN code cell HTML
     *
     * @param int $productId
     * @param string $hsnCode
     * @param string $hsnDisplay
     * @return string
     */
    protected function createHsnCell($productId, $hsnCode, $hsnDisplay)
    {
        $hsnCell = '<td class="col-hsn-code" data-product-id="' . $productId . '">';
        $hsnCell .= '<div class="hsn-container">';
        $hsnCell .= '<span class="hsn-display">' . $hsnDisplay . '</span>';
        $hsnCell .= '<input type="text" class="hsn-input" value="' . ($hsnCode ?: '') . '" style="display:none;" />';
        $hsnCell .= '<div class="hsn-buttons">';
        $hsnCell .= '<button type="button" class="hsn-edit-btn action-default" data-action="edit">' . __('Edit') . '</button>';
        $hsnCell .= '<button type="button" class="hsn-save-btn action-primary" data-action="save" style="display:none;">' . __('Save') . '</button>';
        $hsnCell .= '<button type="button" class="hsn-cancel-btn action-secondary" data-action="cancel" style="display:none;">' . __('Cancel') . '</button>';
        $hsnCell .= '</div>';
        $hsnCell .= '</div>';
        $hsnCell .= '</td>';
        
        return $hsnCell;
    }

    /**
     * Get JavaScript and CSS for HSN code functionality
     *
     * @return string
     */
    protected function getHsnCodeScripts()
    {
        return '
<script type="text/javascript">
require([
    "jquery",
    "Magento_Ui/js/modal/alert",
    "mage/backend/notification"
], function($, alert, notification) {
    "use strict";
    
    $(document).ready(function() {
        // Handle edit button click
        $(document).on("click", ".hsn-edit-btn", function() {
            var $row = $(this).closest("td");
            var $display = $row.find(".hsn-display");
            var $input = $row.find(".hsn-input");
            var $editBtn = $row.find(".hsn-edit-btn");
            var $saveBtn = $row.find(".hsn-save-btn");
            var $cancelBtn = $row.find(".hsn-cancel-btn");
            
            $display.hide();
            $editBtn.hide();
            $input.show().focus();
            $saveBtn.show();
            $cancelBtn.show();
        });
        
        // Handle cancel button click
        $(document).on("click", ".hsn-cancel-btn", function() {
            var $row = $(this).closest("td");
            var $display = $row.find(".hsn-display");
            var $input = $row.find(".hsn-input");
            var $editBtn = $row.find(".hsn-edit-btn");
            var $saveBtn = $row.find(".hsn-save-btn");
            var $cancelBtn = $row.find(".hsn-cancel-btn");
            
            $display.show();
            $editBtn.show();
            $input.hide();
            $saveBtn.hide();
            $cancelBtn.hide();
            
            // Reset input value
            var originalValue = $display.text() === "Not Set" ? "" : $display.text();
            $input.val(originalValue);
        });
        
        // Handle save button click
        $(document).on("click", ".hsn-save-btn", function() {
            var $button = $(this);
            var $row = $button.closest("td");
            var productId = $row.data("product-id");
            var $input = $row.find(".hsn-input");
            var hsnCode = $input.val().trim();
            
            // Validate HSN code format
            if (hsnCode && !/^\d{4,8}$/.test(hsnCode)) {
                alert({
                    title: "' . __('Validation Error') . '",
                    content: "' . __('Please enter a valid HSN code (4-8 digits)') . '"
                });
                return;
            }
            
            // Show loading state
            $button.text("' . __('Saving...') . '").prop("disabled", true);
            
            // Make AJAX request
            $.ajax({
                url: "' . $this->getAjaxUrl() . '",
                type: "POST",
                data: {
                    product_id: productId,
                    hsn_code: hsnCode,
                    form_key: window.FORM_KEY
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        var $display = $row.find(".hsn-display");
                        $display.text(hsnCode || "Not Set");
                        
                        // Reset UI
                        $row.find(".hsn-cancel-btn").click();
                        
                        // Show success notification
                        notification().add({
                            error: false,
                            message: "' . __('HSN code saved successfully') . '"
                        });
                    } else {
                        alert({
                            title: "' . __('Error') . '",
                            content: "' . __('Error saving HSN code: ') . '" + (response.message || "' . __('Unknown error') . '")
                        });
                    }
                },
                error: function() {
                    alert({
                        title: "' . __('Error') . '",
                        content: "' . __('Error saving HSN code. Please try again.') . '"
                    });
                },
                complete: function() {
                    $button.text("' . __('Save') . '").prop("disabled", false);
                }
            });
        });
        
        // Handle Enter key in input
        $(document).on("keypress", ".hsn-input", function(e) {
            if (e.which === 13) { // Enter key
                $(this).closest("td").find(".hsn-save-btn").click();
            }
        });
        
        // Handle Escape key in input
        $(document).on("keyup", ".hsn-input", function(e) {
            if (e.which === 27) { // Escape key
                $(this).closest("td").find(".hsn-cancel-btn").click();
            }
        });
    });
});
</script>

<style type="text/css">
.col-hsn-code {
    text-align: center;
    width: 150px;
    white-space: nowrap;
}

.hsn-input {
    width: 80px;
    padding: 4px 6px;
    border: 1px solid #adadad;
    border-radius: 1px;
    font-size: 1.3rem;
    line-height: 1.36;
}

.hsn-edit-btn, .hsn-save-btn, .hsn-cancel-btn {
    margin: 2px;
    padding: 4px 8px;
    font-size: 1.2rem;
    line-height: 1.33;
    border-radius: 1px;
    cursor: pointer;
    border: 1px solid transparent;
}

.hsn-edit-btn {
    background: #f8f8f8;
    border-color: #adadad;
    color: #333;
}

.hsn-save-btn {
    background: #007bdb;
    border-color: #007bdb;
    color: #fff;
}

.hsn-cancel-btn {
    background: #666;
    border-color: #666;
    color: #fff;
}

.hsn-edit-btn:hover {
    background: #e9e9e9;
}

.hsn-save-btn:hover {
    background: #0066cc;
}

.hsn-cancel-btn:hover {
    background: #555;
}

.hsn-display {
    font-weight: 500;
    color: #333;
}
</style>';
    }

    /**
     * Get AJAX URL for saving HSN code
     *
     * @return string
     */
    protected function getAjaxUrl()
    {
        return $this->urlBuilder->getUrl('invoicehsn/ajax/save');
    }
}