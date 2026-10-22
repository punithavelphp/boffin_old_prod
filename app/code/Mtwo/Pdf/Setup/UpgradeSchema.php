<?php
/**
 * PHP version 7
 * @category  Mtwo
 * @package  Mtwo_Pdf
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Mtwo\Pdf\Setup;

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
				$table = $installer->getConnection()->newTable($installer->getTable('custom_invoice'))
				->addColumn('id', Table::TYPE_INTEGER, 11, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
				],
					'ID'
				)->addColumn('invoice_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Manager ID'
 				)->addColumn('custom_invoice_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Order ID'
 				)->addColumn(
					'invoice_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
					null,
					['nullable' => true],
					'Invoice Date'
				)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				)  ->setComment('Custom Invoice Data');
			$installer->getConnection()->createTable($table);
			 
			 
			 $installer->getConnection()->addColumn(
				$setup->getTable('marketplace_orders'),
				'shipping_charges_tax',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'nullable' => false,
					'LENGTH' =>'12,4',
					 'comment' => 'shipping_charges_tax'
				]
      		 );
			 
			 
			 
		 }
		 
		 
        $installer->endSetup();
    }
}