<?php
namespace Magecomp\Mobileloginfree\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $customerSetupFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

	public function upgrade( ModuleDataSetupInterface $setup, ModuleContextInterface $context ) {
		$installer = $setup;

		$installer->startSetup();

		if(version_compare($context->getVersion(), '1.0.1', '<')) {
			$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
			// Institue
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'institute',
	            [
	                'label' => 'Institute',
	                'input' => 'select',
	                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
	                'required' => false,
	                'sort_order' => 151,
	                'position' => 151,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true,
	                'option' => ['values' => ['Select']]
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'institute');

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

	        // Institue Others
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'institute_others',
	            [
	                'label' => 'Others (Institute)',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 152,
	                'position' => 152,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'institute_others');

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

	        // Department
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'department',
	            [
	                'label' => 'Department',
	                'input' => 'select',
	                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
	                'required' => false,
	                'sort_order' => 153,
	                'position' => 153,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true,
	                'option' => ['values' => ['Select']]
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'department');

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

	        // Department Others
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'department_others',
	            [
	                'label' => 'Others (Department)',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 154,
	                'position' => 154,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'department_others');

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

	        // Faculty
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'faculty',
	            [
	                'label' => 'Faculty',
	                'input' => 'select',
	                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
	                'required' => false,
	                'sort_order' => 155,
	                'position' => 155,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true,
	                'option' => ['values' => ['Select']]
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'faculty');

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

	        // Faculty Others
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'faculty_others',
	            [
	                'label' => 'Others (Faculty)',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 156,
	                'position' => 156,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'faculty_others');

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
	        // Lab Address
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'lab_address',
	            [
	                'label' => 'Lab Address',
	                'input' => 'textarea',
	                'required' => false,
	                'sort_order' => 157,
	                'position' => 157,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'lab_address');

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
	        // GSTIN
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'gstin_number',
	            [
	                'label' => 'CIN/GSTIN number',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 158,
	                'position' => 158,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'gstin_number');

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
	        // Supervisor email
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'supervisor_email',
	            [
	                'label' => 'Supervisor email',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 159,
	                'position' => 159,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'supervisor_email');

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
	        // Manager 1 email
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'manager1_email',
	            [
	                'label' => 'Manager 1 email',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 160,
	                'position' => 160,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'manager1_email');

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
	        // Manager 2 email
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'manager2_email',
	            [
	                'label' => 'Manager 2 email',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 161,
	                'position' => 161,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'manager2_email');

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
	        // User Type
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'user_type',
	            [
	                'label' => 'User Type',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 162,
	                'position' => 162,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'user_type');

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

	        // Fax number
	        $customerSetup->addAttribute(
	            Customer::ENTITY,
	            'fax_number',
	            [
	                'label' => 'Fax number',
	                'input' => 'text',
	                'required' => false,
	                'sort_order' => 163,
	                'position' => 163,
	                'visible' => true,
	                'system' => false,
	                'unique' => true,
	                'is_used_in_grid' => true,
	                'is_visible_in_grid' => true,
	                'is_filterable_in_grid' => true,
	                'is_searchable_in_grid' => true
	            ]
	        );

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'fax_number');

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
		}
		$installer->endSetup();
	}
}