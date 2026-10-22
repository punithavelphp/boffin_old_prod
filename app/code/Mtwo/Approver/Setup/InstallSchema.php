<?php

namespace Mtwo\Approver\Setup;

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

		$installer->run('CREATE TABLE  `approvers` (
		  `id` INT(11) NOT NULL AUTO_INCREMENT,
		  `employer_id` INT(11) DEFAULT NULL,
		  `employee_id` INT(11) DEFAULT NULL,
		  `priority` smallint(10) NOT NULL,
		  `created_at` DATETIME DEFAULT NULL,
		  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
		  PRIMARY KEY (`id`) 
		 
		) ENGINE=INNODB DEFAULT CHARSET=utf8mb4');


		 

		}

        $installer->endSetup();

    }
}