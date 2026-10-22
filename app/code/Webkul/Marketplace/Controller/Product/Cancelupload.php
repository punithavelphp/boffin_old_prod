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
class Cancelupload extends Action
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
		HelperData $helper
	) {
		$this->_customerSession = $customerSession;
		parent::__construct(
			$context
		);
		$this->productBuilder = $productBuilder;
		$this->_resultPageFactory = $resultPageFactory;
		$this->customerUrl = $customerUrl;
		$this->helper = $helper;
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
			$customerId = $helper->getCustomerId();
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$tableName = $resource->getTableName('csvdata'); 

			$sql = "Update " . $tableName . " set `status` = 3 where `customer_id` = '".$customerId."'";
			$connection->query($sql);
			
		}
		return $this->resultRedirectFactory->create()->setPath(
			'marketplace/account/addproducts',
			['_secure' => $this->getRequest()->isSecure()]
		);

	}
}