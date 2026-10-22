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
namespace Webkul\Marketplace\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Webkul\Marketplace\Helper\Data as HelperData;

/**
 * Webkul Marketplace Product Add Controller.
 */
class Runuploads extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Array of actions which can be processed without secret key validation.
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @param Context                                       $context
     * @param Webkul\Marketplace\Controller\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory    $resultPageFactory
     * @param \Magento\Customer\Model\Session               $customerSession
     * @param CustomerUrl                                   $customerUrl
     * @param HelperData                                    $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Webkul\Marketplace\Controller\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        CustomerUrl $customerUrl,
        HelperData $helper,
		\Webkul\Marketplace\Controller\Product\Saveuploads $saveuploads
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context
        );
        $this->productBuilder = $productBuilder;
        $this->_resultPageFactory = $resultPageFactory;
        $this->customerUrl = $customerUrl;
        $this->helper = $helper;
		$this->saveuploads = $saveuploads;

    }
 

    /**
     * Seller Product Add Action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
          $response =  $this->saveuploads->createProducts();
			if(isset($response['data'])){
			foreach($response['data'] as $p){
				 $productIds[] = $p['product_id'];
			}
				
				}
			 
			$indexLists = ['catalog_category_product', 'catalog_product_category', 'catalog_product_attribute', 'cataloginventory_stock', 'inventory', 'catalogsearch_fulltext', 'catalog_product_price', 'catalogrule_product', 'catalogrule_rule'];
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

foreach ($indexLists as $indexList) {
    $categoryIndexer = $objectManager->get('Magento\Framework\Indexer\IndexerRegistry')->get($indexList);

    if ($categoryIndexer->isScheduled()) {
    try {
		
       //  $categoryIndexer->reindexList(array_unique($productIds));
     
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    }
}
			
			
			echo json_encode($response);
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
