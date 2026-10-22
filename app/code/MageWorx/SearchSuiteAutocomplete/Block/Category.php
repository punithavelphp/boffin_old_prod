<?php

namespace MageWorx\SearchSuiteAutocomplete\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory;

class Category extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
	/**
	* @var \Magento\Customer\Model\Session
	*/
	protected $customerSession;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;

	/**
	 * @var array
	 */
	protected $_categories_links = [];

	/**
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	protected $_categoryFactory;

	/**
	 * @var \Magento\Search\Model\ResourceModel\Query\CollectionFactory
	 */
	protected $_queryCollectionFactory;

	/**
	 * @var array
	 */
	protected $_terms;

	/**
	 * @var \Lof\Autosearch\Helper\Data
	 */
	protected $_autosearchHelper;

	protected $_is_widget = false;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context                
	 * @param CustomerSession                                  $customerSession        
	 * @param \Magento\Catalog\Model\CategoryFactory           $categoryFactory        
	 * @param CollectionFactory                                $queryCollectionFactory 
	 * @param array                                            $data                   
	 */
	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		CustomerSession $customerSession,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		CollectionFactory $queryCollectionFactory,
	 
		array $data=[]
		){
		$this->customerSession         = $customerSession;
		$this->_categoryFactory        = $categoryFactory;
		$this->_queryCollectionFactory = $queryCollectionFactory;
 		$this->scopeConfig             = $context->getScopeConfig();
		parent::__construct($context, $data);
	}

	
	public function getTreeCategories($parentId,$level = 0, $caret = '  ', $maxLevel = 3){
		$category_id = $this->getRequest()->getParam("category_ids");
  		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$current_category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
		if(!$category_id && $current_category){
			$category_id = $current_category->getId();
		}
 		$allCats = $this->_categoryFactory->create()->getCollection()
		->addAttributeToSelect('*')
		->addAttributeToFilter('is_active','1')
		->addAttributeToSort('position', 'asc');
 		 $allCats->addAttributeToFilter('include_in_menu','1');
 		if ($parentId) {
			$allCats->addAttributeToFilter('parent_id',array('eq' => $parentId));
		}
		
	 
		$html= '';
		$prefix = "";
		if($level) {
			for($i=0;$i < $level; $i++) {
				$prefix .= $caret;
			}
		}
		foreach($allCats as $category)
		{
			if(!isset($this->_categories_links[$category->getId()])) {
				$this->_categories_links[$category->getId()] = $category->getId();
				$subcats = $category->getChildren();
				$html .= '<option value="'.$category->getId().'" '.($category_id == $category->getId() ? 'selected="selected"':'') .'>'.$prefix.$category->getName().'</option>';
				$subcats = $category->getChildren();
				if ($subcats != '' && ((int)$level + 1) < $maxLevel) { 
					$html .= $this->getTreeCategories($category->getId(), (int)$level + 1, $caret.'&nbsp;', $maxLevel);
				}

			}

		}
		return $html;
	}
	
}

?>