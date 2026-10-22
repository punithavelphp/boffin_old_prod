<?php

namespace Magecomp\Mobileloginfree\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;


class InstallData implements InstallDataInterface
{
    const MOBILENUMBER = 'mobilenumber';

    private $customerSetupFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup,ModuleContextInterface $context)
    {
        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::MOBILENUMBER,
            [
                'label' => 'Mobile Number',
                'input' => 'text',
                'required' => false,
                'sort_order' => 150,
                'position' => 150,
                'visible' => true,
                'system' => false,
                'unique' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,self::MOBILENUMBER);

        $usedInForms = [
            'adminhtml_customer',
            'checkout_register',
            'customer_account_create',
            'customer_account_edit',
            'adminhtml_checkout'
        ];
        $attribute->setData('used_in_forms', $usedInForms)
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', 0)
            ->setData('is_user_defined', 1);

        $attribute->save();

        $setup->endSetup();
    }
}
