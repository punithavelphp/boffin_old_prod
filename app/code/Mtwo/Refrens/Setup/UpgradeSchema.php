<?php
namespace Mtwo\Refrens\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            // Your table creation script goes here
            $tableName = $setup->getTable('gst_states');
            if (!$setup->getConnection()->isTableExists($tableName)) {
                $table = $setup->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'code',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        11,
                        ['nullable' => false, 'primary' => true],
                        'State Code'
                    )
                    ->addColumn(
                        'name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'State Name'
                    )
                    ->setComment('gst states Table');
                $setup->getConnection()->createTable($table);
            }
        }

        $setup->endSetup();
    }
}
