<?php
/**
 * PHP version 7
 * @category  EMC
 * @package  Emc_Otp
 * @author  Sai Ram <sairam@egrovesys.com>
 */

namespace Mtwo\Quote\Setup;

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
			  $installer->getConnection()->addColumn(
				$setup->getTable('custom_quote'),
				'is_quote_cart',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'default' => 1,
 					'comment' => 'is_quote_cart'
				]
      		 );
		 }
        $installer->endSetup();
    }
}