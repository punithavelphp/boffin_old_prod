<?php
namespace Magecomp\Mobileloginfree\Helper;

use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MOBILELOGINFREE_GENERAL_ENABLED = 'mobileloginfree/general/enable';
    const MOBILELOGINFREE_GENERAL_LGGINMODE = 'mobileloginfree/general/loginmode';

    protected $_storeManager;
    protected $filterBuilder;
    protected $searchCriteriaBuilder;
    protected $customerRepository;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\Api\FilterBuilder $filterBuilder,
                                \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
                                \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository)
    {
        $this->_storeManager = $storeManager;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    public function getStoreid()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::MOBILELOGINFREE_GENERAL_ENABLED,
                ScopeInterface::SCOPE_STORE,
                $this->getStoreid());
    }

    public function getLoginMode()
    {
        return $this->scopeConfig->getValue(self::MOBILELOGINFREE_GENERAL_LGGINMODE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid());
    }

    public function getCustomerShareScope()
    {
        return $this->scopeConfig->getValue(ConfigShare::XML_PATH_CUSTOMER_ACCOUNT_SHARE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    public function filterWebsiteShare()
    {
        if ($this->getCustomerShareScope() == ConfigShare::SHARE_WEBSITE) {
            return $this->filterBuilder
                ->setField('website_id')
                ->setConditionType('eq')
                ->setValue($this->_storeManager->getStore()->getWebsiteId())
                ->create();
        }
        return false;
    }

    public function getByMobilenumber(string $mobilenumber)
    {
        $websiteIdFilter[] = $this->filterWebsiteShare();

        // Add customer attribute filter
        $customerFilter[] = $this->filterBuilder
            ->setField(\Magecomp\Mobileloginfree\Setup\InstallData::MOBILENUMBER)
            ->setConditionType('eq')
            ->setValue($mobilenumber)
            ->create();

        // Build search criteria
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilters($customerFilter);
        if (!empty($websiteIdFilter)) {
            $searchCriteriaBuilder->addFilters($websiteIdFilter);
        }
        $searchCriteria = $searchCriteriaBuilder->create();

        // Retrieve customer collection.
        $collection = $this->customerRepository->getList($searchCriteria);
        if ($collection->getTotalCount() == 1) {
            // Return first occurrence.
            $accounts = $collection->getItems();
            return reset($accounts);
        }
        return false;
    }
}