<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Emc\Otp\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @package Emc\Otp\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
		 if (version_compare($context->getVersion(), '2.0.0', '<')) {
				$table = $installer->getConnection()->newTable($installer->getTable('emc_otp_count'))
				->addColumn('id', Table::TYPE_INTEGER, 11, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
				],
					'ID'
				)->addColumn('mobile_number', Table::TYPE_TEXT, 15,
					['nullable => false'],
					'Mobile Number'

				)->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				) ->addColumn('count', Table::TYPE_INTEGER, 4, [
					'nullable' => false,
					'unsigned' => true,
				],
					'ID'
				)->setComment('Otp Count Table');
			$installer->getConnection()->createTable($table);
		 }
		 if (version_compare($context->getVersion(), '2.0.1', '<')) {
				 $installer->getConnection()
			->addColumn(
                            $installer->getTable('emc_otp_count'), 'prefix', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 5,
                         'nullable' => true,
                        'comment' => 'Mobile Prefix'
                            ]
            );
		 }
		if (version_compare($context->getVersion(), '3.0.0', '<')) {
			$table = $installer->getConnection()
                ->newTable($installer->getTable('sms_log'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Id'
                )
               ->addColumn(
                'sent_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Sent Date'
            )
                ->addColumn(
                    'order_no',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    32,
                    [
                    ],
                    'Order No'
                )
  		    ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false, 'default' => ''],
                'Type'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Title'
            )->addColumn(
                'sender_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Sender Id'
            )->addColumn(
                'to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'To'
            )->addColumn(
                'recipient',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Recipient'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                ['nullable' => false, 'default' => 0],
                'Customer Id'
            )
	        ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Message'
            )
	        ->addColumn(
                'length',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'Length'
            )
		    ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'Status'
            )
		    ->addColumn(
                'chars',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                ['nullable' => false],
                'Chars'
            )
		    ->addColumn(
                'api_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Api Status'
            )
                ->setComment('Emc Sms Log');
			$installer->getConnection()->createTable($table);
		}
        $installer->endSetup();
    }
}