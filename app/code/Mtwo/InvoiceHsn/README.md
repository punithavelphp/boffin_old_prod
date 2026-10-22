# Mtwo InvoiceHsn Module

## Description
This Magento 2 module adds HSN (Harmonized System Nomenclature) code functionality to adminhtml invoice items. It allows administrators to view and edit HSN codes directly from the invoice view in the admin panel.

## Features
- Display HSN codes in invoice items grid
- Inline editing of HSN codes in adminhtml
- AJAX-powered saving of HSN codes to products
- Validation of HSN code format (4-8 digits)
- Automatic formatting and display of HSN codes
- Permission-based access control

## Requirements
- Magento 2.3+
- PHP 7.3+ or 8.1+
- Product attribute 'hsncode' should already exist

## Installation
1. Copy the module files to `app/code/Mtwo/InvoiceHsn/`
2. Run the following commands:
   ```bash
   php bin/magento module:enable Mtwo_InvoiceHsn
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento cache:clean
   ```

## Usage
1. Navigate to Sales > Orders > Select an Order > View Invoice
2. In the invoice items section, you'll see a new "HSN Code" column
3. Click the "Edit" button next to any HSN code to modify it
4. Enter a valid HSN code (4-8 digits) and click "Save"
5. The HSN code will be saved to the product and displayed in future invoices

## File Structure
```
Mtwo/InvoiceHsn/
├── Block/
│   └── Adminhtml/
│       └── Order/
│           └── Invoice/
│               └── View/
│                   └── Items.php
├── Controller/
│   └── Adminhtml/
│       └── Ajax/
│           └── Save.php
├── Helper/
│   └── Data.php
├── Plugin/
│   └── Sales/
│       └── Block/
│           └── Adminhtml/
│               └── Order/
│                   └── Invoice/
│                       └── View/
│                           └── Items.php
├── etc/
│   ├── adminhtml/
│   │   └── routes.xml
│   ├── acl.xml
│   ├── di.xml
│   └── module.xml
├── view/
│   └── adminhtml/
│       ├── layout/
│       │   └── sales_order_invoice_view.xml
│       └── templates/
│           └── order/
│               └── invoice/
│                   └── view/
│                       └── items.phtml
├── composer.json
├── README.md
└── registration.php
```

## Configuration
The module works with the existing 'hsncode' product attribute. If this attribute doesn't exist, you'll need to create it first:

```php
// Example of creating hsncode attribute
$eavSetup->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'hsncode',
    [
        'type' => 'varchar',
        'label' => 'HSN Code',
        'input' => 'text',
        'required' => false,
        'sort_order' => 100,
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'used_in_product_listing' => false,
        'visible_on_front' => false,
    ]
);
```

## Permissions
The module respects Magento's ACL system. Users need the "Sales > Invoice" permission to access HSN code editing functionality.

## Troubleshooting
- Ensure the 'hsncode' product attribute exists
- Check that the module is enabled: `php bin/magento module:status`
- Verify file permissions
- Clear cache after installation

## Support
For support and bug reports, please contact the development team.

## License
Proprietary - All rights reserved.