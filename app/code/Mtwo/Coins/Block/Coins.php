<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Block;

class Coins extends \Magento\Framework\View\Element\Template
{
	public $catalogFactory;
	public $storeManager;
	public $customerSession;
	public $coinsOrders;
	public $coins;
	public $status;


    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Mtwo\Coins\Model\CatalogFactory $catalogFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		 \Magento\Customer\Model\Session $customerSession,
		\Mtwo\Coins\Model\OrdersFactory $coinsOrders,
		\Mtwo\Coins\Model\Config\Source\Status $status,
		\Mtwo\Coins\Model\Coins $coins,
        array $data = []
    ) {
		$this->catalogFactory = $catalogFactory;
		$this->storeManager = $storeManager;
		$this->customerSession = $customerSession;
		$this->coinsOrders = $coinsOrders;
		$this->coins = $coins;
		$this->status = $status;
        parent::__construct($context, $data);
    }
	
	 
	public function getMediaUrl()
	{
		return $mediaUrl = $this->storeManager
						 ->getStore()
						 ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

	}
	public function getOptions()
	{
		return $mediaUrl = $this->status
						 ->getOptions();

	}
	
	protected function _prepareLayout()
	{
	 
		parent::_prepareLayout();
		$page_size = $this->getPagerCount();
		$page_data = $this->getCustomData();
		if ($this->getCustomData()) {
			$pager = $this->getLayout()->createBlock(
				\Magento\Theme\Block\Html\Pager::class,
				'coins.pager.name'
			)
				->setAvailableLimit($page_size)
				->setShowPerPage(true)
				->setCollection($page_data);
			$this->setChild('pager', $pager);
			$this->getCustomData()->load();
		}
		return $this;
	}
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function getPageSize(){

		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		return $page;
	}

	public function getPageLimit(){

		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
		return $pageSize;
	}
	public function getCustomData()
	{
		// get param values
		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5; // set minimum records
		// get custom collection

 		$customer_id = $this->customerSession->getCustomer()->getId();
		 

		$collection = $this->coinsOrders->create()->getCollection()->addFieldToFilter('customer_id',$customer_id);
			 $collection->getSelect()->joinLeft( array( 'cat' => 'mtwo_coins_catalog' ), 'main_table.catalog_id = cat.catalog_id', array('name'=>'name')); 
			;
 		$collection->setPageSize($pageSize);
		$collection->setCurPage($page);
		$collection->setOrder('created_at','desc');
 		$collection->load();
		// echo $collection->getSelect();
		
		return $collection;
	}
	public function getEarnedList()
	{
		// get param values
		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5; // set minimum records
		// get custom collection

 		$customer_id = $this->customerSession->getCustomer()->getId();
		 

		$collection = $this->coins->getCollection()->addFieldToFilter('customer_id',$customer_id);
			// $collection->getSelect()->joinLeft( array( 'cat' => 'mtwo_coins_catalog' ), 'main_table.catalog_id = cat.catalog_id', array('name'=>'name')); 
			;
 		$collection->setPageSize($pageSize);
		$collection->setCurPage($page);
		$collection->setOrder('created_at','desc');
 		$collection->load();
		// echo $collection->getSelect();
		
		return $collection;
	}
	
	public function getAvailableBalance()
	{
	 
 		$customer_id = $this->customerSession->getCustomer()->getId();
		 

		$collection = $this->coins->getCollection()
			->addFieldToFilter('main_table.customer_id',$customer_id)
			->addFieldToFilter('main_table.is_approved',1)
			
			;
 		$collection->setOrder('created_at','desc');
 		 
	 
		$earnedAmount = 0;
		$redeemedAmount = 0;
		$total = 0;
		foreach($collection as $coll){
			$earned = $coll->getData('coins');
			//echo $earned;
			
			if($earned > 0){
				$earnedAmount +=$earned;
			}
			else{
				$redeemedAmount +=$earned;
			}
			$total += $earned;
		}
		$data = array('earned'=>$earnedAmount,'redeemed'=>$redeemedAmount,'total'=>$total);
		
		return $data;
	}

	public function getPagerCount()
	{
		// get collection
		$minimum_show = 5; // set minimum records
		$page_array = [];
		
 		$customer_id = $this->customerSession->getCustomer()->getId();
 		 $list_data = $this->coinsOrders->create()->getCollection()->addFieldToFilter('customer_id',$customer_id);

		$list_count = ceil(count($list_data->getData()));
		$show_count = $minimum_show + 1;
		if (count($list_data->getData()) >= $show_count) {
			$list_count = $list_count / $minimum_show;
			$page_nu = $total = $minimum_show;
			$page_array[$minimum_show] = $minimum_show;
			for ($x = 0; $x <= $list_count; $x++) {
				$total = $total + $page_nu;
				$page_array[$total] = $total;
			}
		} else {
			$page_array[$minimum_show] = $minimum_show;
			$minimum_show = $minimum_show + $minimum_show;
			$page_array[$minimum_show] = $minimum_show;
		}
		return $page_array;
	}

	
	
}

