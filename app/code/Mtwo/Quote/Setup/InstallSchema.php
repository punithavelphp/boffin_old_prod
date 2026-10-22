<?php

namespace Mtwo\Quote\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){
 
 				
				$tableName = $installer->getTable ( 'custom_quote' );
 			 
 					$table = $installer->getConnection ()->newTable ( $tableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
					'identity' => true,
					'nullable' => false,
					'primary' => true
					], 'ID' )
					->addColumn ( 'customer_id', Table::TYPE_INTEGER, 11, [
					'nullable' => false
					], 'Customer ID' )  
					->addColumn ( 'mobile', Table::TYPE_TEXT, '20', [
					'nullable' => false
					], 'Customer ID' )  
					->addColumn ( 'email', Table::TYPE_TEXT, '255', [
					'nullable' => false
					], 'sku' )   
 					
					->addColumn ( 'status', Table::TYPE_SMALLINT, 4, [
					'default' => 0 
					], 'Yet to confirm' )
					
					->addColumn( 'created_date', Table::TYPE_TIMESTAMP, null, ['nullable' => false,'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'created date'
                    )
					->addColumn ( "last_modified", \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array (
					"default" => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
					), "Last Modified" )
						 

						 
					->setComment ( $tableName )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
					$installer->getConnection ()->createTable ( $table );
				 
			
			$tableName = $installer->getTable ( 'custom_quote_items' );
 			 
 					$table = $installer->getConnection ()->newTable ( $tableName )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
					'identity' => true,
					'nullable' => false,
					'primary' => true
					], 'ID' )
					->addColumn ( 'product_id', Table::TYPE_INTEGER, 11, [
					'nullable' => false
					], 'Customer ID' )
					->addColumn ( 'quote_id', Table::TYPE_INTEGER, 11, [
					'nullable' => false
					], 'quote id' )
					->addColumn ( 'sku', Table::TYPE_TEXT, '100', [
					'nullable' => false
					], 'Customer ID' )  
					->addColumn ( 'brand', Table::TYPE_TEXT, '150', [
					'nullable' => false
					], 'sku' )   
 						->addColumn ( 'sellerid', Table::TYPE_INTEGER, 11, [
					'nullable' => false
					], 'sellerid' ) 
							->addColumn ( 'price', Table::TYPE_DECIMAL, '10,4', [
					'nullable' => false
					], 'price' ) 
							->addColumn ( 'final_price', Table::TYPE_DECIMAL, '10,4', [
					'nullable' => false
					], 'final_price' ) 
							->addColumn ( 'qty', Table::TYPE_INTEGER,  11, [
					'nullable' => false
					], 'sku' ) 
 					->addColumn( 'created_date', Table::TYPE_TIMESTAMP, null, ['nullable' => false,'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'created date'
                    )
					->addColumn ( "last_modified", \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array (
					"default" => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
					), "Last Modified" )
						 
					->setComment ( $tableName )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
					$installer->getConnection ()->createTable ( $table );
  				
		 
			
		 

		}

        $installer->endSetup();

    }
}