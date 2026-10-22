<?php
declare(strict_types=1);

namespace Magelearn\Customform\Model\ResourceModel\Customform;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';
	protected $_previewFlag;
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magelearn\Customform\Model\Customform::class,
            \Magelearn\Customform\Model\ResourceModel\Customform::class
        );
		$this->_map['fields']['id'] = 'main_table.id';
    }
}

