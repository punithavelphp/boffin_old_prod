<?php 

namespace Mtwo\ProductUpload\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Mtwo\Approver\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (version_compare($context->getVersion(), '2.0.0', '<')) {
			$installer->getConnection()->addColumn(
				$setup->getTable('sales_invoice'),
				'po_number',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'comment' => 'po_number'
				]
			);
			$installer->getConnection()->addColumn(
				$setup->getTable('sales_invoice'),
				'po_date',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
					'nullable' => true,
					'comment' => 'po_date'
				]
			);
		}
		if (version_compare($context->getVersion(), '2.1.0', '<')) {
			$installer->getConnection()->addColumn(
				$setup->getTable('sales_invoice'),
				'shipped_from',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'comment' => 'shipped_from'
				]
			);
		 
		}
		$installer->endSetup();
	}
}