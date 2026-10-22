<?php

namespace Mtwo\Refrens\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

		 $tableName = $installer->getTable('sales_invoice_refrens');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create al_uv_truck_details table
            $table = $installer->getConnection()->newTable(
					$installer->getTable($tableName)
					)->addColumn(
					'id',  Table::TYPE_INTEGER, null, [
					'identity' => true,
					'nullable' => false,
					'primary' => true,
					'unsigned' => true,
					], 'ID'
					)->addColumn(
                            'increment_id', Table::TYPE_TEXT, 20, ['nullable' => false], 'invoice increment_id'
                  
					)->addColumn(
                            'invoice_id', Table::TYPE_INTEGER, 11, ['nullable' => false], 'invoice id'
                     
					)->addColumn(
                            'refrens_pdf_url', Table::TYPE_TEXT, '1M', ['nullable' => true], 'refrens_pdf_url'
                    )
                    ->addColumn(
                            'refrens_link_url', Table::TYPE_TEXT, '1M', ['nullable' => true], 'refrens_link_url'
                    )
					->addColumn(
                            'refrens_challan_pdf_url', Table::TYPE_TEXT, '1M', ['nullable' => true], 'refrens_challan_pdf_url'
                    )
				->addColumn(
                            'refrens_challan_link_url', Table::TYPE_TEXT, '1M', ['nullable' => true], 'refrens_challan_link_url'
                    )
					->addColumn(
                            'refrens_challan_id', Table::TYPE_TEXT, 255, ['nullable' => true], 'refrens_invoice_id'
                    ) 
                  ->addColumn(
                            'refrens_invoice_id', Table::TYPE_TEXT, 255, ['nullable' => true], 'refrens_invoice_id'
                    ) 
				 ->addColumn(
                             'refrens_submitted_on', Table::TYPE_TIMESTAMP, null, ['nullable' => true], 'refrens_submitted_on'
                    )
                   

                    ->setComment('refrens')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
         $installer->endSetup();
    }

}
