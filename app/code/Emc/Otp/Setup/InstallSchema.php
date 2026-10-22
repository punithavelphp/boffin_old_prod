<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Emc\Otp\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @package Emc\Otp\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable($installer->getTable('emc_otp'))
            ->addColumn('id', Table::TYPE_INTEGER, 11, [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
            ],
                'ID'
            )->addColumn('customer_email', Table::TYPE_TEXT, 255,
                ['unsigned' => true, 'nullable => false'],
                'Customer Email'
            )->addColumn('status', Table::TYPE_BOOLEAN,
                null, [
                    'nullable' => false,
                    'default' => true
                ],
                'Status'
            )->addColumn('mobile_number', Table::TYPE_TEXT, 15,
                ['nullable => false'],
                'Mobile Number'

            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            ) ->addColumn('otp', Table::TYPE_INTEGER, 8, [
                'nullable' => false,
                'unsigned' => true,
            ],
                'ID'
            )->setComment('Otp Table');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}