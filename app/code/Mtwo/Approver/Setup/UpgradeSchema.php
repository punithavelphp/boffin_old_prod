<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Mtwo\Approver\Setup;

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
				$table = $installer->getConnection()->newTable($installer->getTable('approved_by'))
				->addColumn('id', Table::TYPE_INTEGER, 11, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
				],
					'ID'
				)->addColumn('manager_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Manager ID'
 				)->addColumn('order_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Order ID'
 				)->addColumn('priority', Table::TYPE_SMALLINT, null,
					['nullable => false'],
					'Priority'
 				)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				)  ->setComment('Approved By Table');
			$installer->getConnection()->createTable($table);
		 } 
		if (version_compare($context->getVersion(), '3.0.0', '<')) {
				$table = $installer->getConnection()->newTable($installer->getTable('employee_order_approver_list'))
				->addColumn('id', Table::TYPE_INTEGER, 11, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
				],
					'ID'
				)->addColumn('manager_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Manager ID'
 				)->addColumn('employee_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Manager ID'
 				)->addColumn('owner_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Manager ID'
 				) ->addColumn('priority', Table::TYPE_SMALLINT, null,
					['nullable => false'],
					'Priority'
 				)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				)  ->setComment('Approved By Table');
			$installer->getConnection()->createTable($table);
		 }
		if (version_compare($context->getVersion(), '3.5.0', '<')) {
				$table = $installer->getConnection()->newTable($installer->getTable('seller_city'))
				->addColumn('id', Table::TYPE_INTEGER, 11, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
				],
					'ID'
				)->addColumn('city_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'City ID'
 				)->addColumn('seller_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'Seller ID'
 				)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				)  ->setComment('Seller City');
			$installer->getConnection()->createTable($table);
		 }
		
		if (version_compare($context->getVersion(), '4.0.0', '<')) {
				$table = $installer->getConnection()->newTable($installer->getTable('faculty_approval'))
				->addColumn('id', Table::TYPE_INTEGER, 11, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
				],
					'ID'
				)->addColumn('order_id', Table::TYPE_INTEGER, 11,
					['nullable => false'],
					'City ID'
 				)->addColumn('increment_id', Table::TYPE_TEXT, 20,
					['nullable => false'],
					'Seller ID'
				)->addColumn('pd_cus_id', Table::TYPE_TEXT, 20,
					['nullable => false'],
					'PD_customer_id'
 				)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				)  ->setComment('Seller City');
			$installer->getConnection()->createTable($table);
		 }
        $installer->endSetup();
    }
}