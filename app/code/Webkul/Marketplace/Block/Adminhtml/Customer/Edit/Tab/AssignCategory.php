<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Framework\Registry;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Webkul\Marketplace\Model\SellerFactory;

class AssignCategory extends \Magento\Config\Block\System\Config\Form\Field
{
    public $coreRegistry;


    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    public $category;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $_categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * Lib data collection factory
     *
     * @var \Magento\Framework\Data\CollectionFactory
     */
    public $_dataCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    public $categoryHelper;

    /**
     * @var \Webkul\Marketplace\Model\SellerFactory
     */
    public $sellerFactory;

    public const ASSIGN_CATEGORY_TEMPLATE = 'customer/assign-category.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param Registry $registry
     * @param Magento\Catalog\Model\Category $category
     * @param Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Data\CollectionFactory $dataCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param CategoryHelper $categoryHelper
     * @param SellerFactory $sellerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Registry $registry,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CategoryHelper $categoryHelper,
        SellerFactory $sellerFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->category = $category;
        $this->_categoryFactory = $categoryFactory;
        $this->_dataCollectionFactory = $dataCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->categoryHelper = $categoryHelper;
        $this->sellerFactory = $sellerFactory;
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::ASSIGN_CATEGORY_TEMPLATE);
        }

        return $this;
    }

    /**
     * GetCategoriesList
     *
     * @return boolean
     */
    public function getCategoriesList()
    {
        // return $this->categoryHelper->getStoreCategories();
        $storeCategoriesArr = [];
        $allStores = $this->_storeManager->getStores();
        foreach ($allStores as $storeId => $store) {
            $parent = $this->_storeManager->getStore($storeId)->getRootCategoryId();
            if (!$parent) {
                $parent = $this->_storeManager->getStore()->getRootCategoryId();
            }
            $parentCat = $this->_categoryFactory->create()->load($parent);

            $sorted = false;
            $asCollection = false;
            $toLoad = true;

            $cacheKey = sprintf(
                '%d-%d-%d-%d',
                $parent,
                $sorted,
                $asCollection,
                $toLoad
            );
            if (isset($this->_storeCategories[$cacheKey])) {
                $storeCategoriesArr[$parent]['parent'] = $parentCat;
                $storeCategoriesArr[$parent]['child'] = $this->_storeCategories[$cacheKey];
                continue;
            }
            $categoryData = $this->_categoryFactory->create();
            if (!$categoryData->checkId($parent)) {
                if ($asCollection) {
                    return $this->_dataCollectionFactory->create();
                }
                return [];
            }
            $navigationMaxDepth = (int)$this->scopeConfig->getValue(
                'catalog/navigation/max_depth',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $recursionLevel = max(0, $navigationMaxDepth);
            $storeCategories = $categoryData->getCategories(
                $parent,
                $recursionLevel,
                $sorted,
                $asCollection,
                $toLoad
            );
            $this->_storeCategories[$cacheKey] = $storeCategories;
            $storeCategoriesArr[$parent]['parent'] = $parentCat;
            $storeCategoriesArr[$parent]['child'] = $this->_storeCategories[$cacheKey];
        }
        return $storeCategoriesArr;
    }

    /**
     * HasChildren
     *
     * @param int $categoryId
     * @return boolean
     */
    public function hasChildren($categoryId)
    {
        $category = $this->category->load($categoryId);
        $childrens = $this->category->getAllChildren($category);
        return count($childrens)-1 > 0 ? true:false;
    }

    /**
     * GetSellerAllowedCategory
     *
     * @return array $category;
     */
    public function getSellerAllowedCategory()
    {
        $sellerId = $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $seller = $this->sellerFactory->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('store_id', 0)
            ->setPageSize(1)
            ->getFirstItem();
        $category = [];
        if ($seller->getEntityId()) {
            $category = $seller->getAllowedCategories() ? explode(',', $seller->getAllowedCategories()) :[];
        }
        return $category;
    }
}
