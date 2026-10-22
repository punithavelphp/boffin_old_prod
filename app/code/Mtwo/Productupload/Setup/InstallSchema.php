<?php namespace Mtwo\Productupload\Setup;
	
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
 				
				$tableName = $installer->getTable ( 'csvdata' );
 			 
 					$table = $installer->getConnection ()->newTable ( $installer->getTable ( 'csvdata' ) )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
					'identity' => true,
					'nullable' => false,
					'primary' => true
					], 'ID' )
					->addColumn ( 'customer_id', Table::TYPE_INTEGER, 11, [
					'nullable' => false
					], 'Customer ID' )  
					->addColumn ( 'category_id', Table::TYPE_INTEGER, 11, [
					'nullable' => false
					], 'Customer ID' )  
					->addColumn ( 'sku', Table::TYPE_TEXT, '200', [
					'nullable' => false
					], 'sku' )   
					->addColumn ( 'data', Table::TYPE_TEXT, '2M', [
					'nullable' => true
					], 'data' )
					
					->addColumn ( 'status', Table::TYPE_SMALLINT, 5, [
					'default' => 0 
					], 'Completed or Need to run' )
					
					->addColumn( 'created_date', Table::TYPE_TIMESTAMP, null, ['nullable' => false,'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'created date'
                    )
					->addColumn ( "last_modified", \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array (
					"default" => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
					), "Last Modified" )
						
						->addIndex(
					$installer->getIdxName(
						$installer->getTable('csvdata'),
						['sku','customer_id'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
					),
					['sku','customer_id'],
					['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
				)

						 
					->setComment ( 'csvdata' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
					$installer->getConnection ()->createTable ( $table );
				 
  				
		 
			
			$installer->endSetup();
			
		}
	}