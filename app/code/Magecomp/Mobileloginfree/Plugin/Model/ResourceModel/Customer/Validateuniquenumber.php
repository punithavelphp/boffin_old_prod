<?php
namespace Magecomp\Mobileloginfree\Plugin\Model\ResourceModel\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer as ResourceModel;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magecomp\Mobileloginfree\Helper\Data as Config;
use Magecomp\Mobileloginfree\Setup\InstallData;


class Validateuniquenumber
{
    private $customerCollectionFactory;
    private $config;

    public function __construct(CustomerCollectionFactory $customerCollectionFactory,Config $config) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->config = $config;
    }

    public function beforeSave(ResourceModel $subject, Customer $customer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $collection = $this->customerCollectionFactory
            ->create()
            ->addAttributeToFilter(InstallData::MOBILENUMBER, $customer->getData(InstallData::MOBILENUMBER));

        // If the customer already exists, exclude them from the query
        if ($customer->getId()) {
            $collection->addAttribuTeToFilter(
                'entity_id',
                [
                    'neq' => (int) $customer->getId(),
                ]
            );
        }

        if ($collection->getSize() > 0) {
            throw new LocalizedException(
                __('A customer with the same mobile number already exists in an associated website.')
            );
        }
    }
}
