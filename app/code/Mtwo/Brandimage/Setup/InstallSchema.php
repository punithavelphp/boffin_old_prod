<?php namespace Mtwo\Brandimage\Setup;
	
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
 				
				$tableName = $installer->getTable ( 'brandimage' );
 			 
 					$table = $installer->getConnection ()->newTable ( $installer->getTable ( 'brandimage' ) )
						->addColumn ( 'id', Table::TYPE_INTEGER, null, [
					'identity' => true,
					'nullable' => false,
					'primary' => true
					], 'ID' ) 
						->addColumn ( 'attribute_id', Table::TYPE_INTEGER, null, [
 					'nullable' => false,
 					], 'attribute_id' ) 
					->addColumn ( 'image', Table::TYPE_TEXT, '200', ['nullable' => true ], 'image' )   
						->addColumn ( 'large_image', Table::TYPE_TEXT, '200', ['nullable' => true ], 'large_image' )   
					->addColumn( 'created_date', Table::TYPE_TIMESTAMP, null, ['nullable' => false,'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'created date'
                    )
					->addColumn ( "last_modified", \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array (
					"default" => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
					), "Last Modified" )
					 ->addIndex(
						$installer->getIdxName(
							$installer->getTable ( 'brandimage' ),
							['attribute_id'],
							\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
						),
						['attribute_id'],
						['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
					)

					->setComment ( 'brandimage' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
					$installer->getConnection ()->createTable ( $table );
				 
  				
		 
			
			$installer->endSetup();
			
		}
	}