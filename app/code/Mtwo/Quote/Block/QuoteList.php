<?php

namespace Mtwo\Quote\Block;

use Magento\Framework\View\Element\Template;

class QuoteList extends Template
{
    public $customQuoteItemsFactory;
    public $customQuoteFactory;
    public $productFactory;
    public $customerSession;

 
    public function __construct(
        Template\Context $context
		,\Mtwo\Quote\Model\QuoteFactory $customQuote
		,\Mtwo\Quote\Model\QuoteItemsFactory $customQuoteItems
		,\Magento\Catalog\Model\ProductFactory $productFactory
		,\Magento\Customer\Model\Session $customerSession

         ,array $data = []
    ) {
        parent::__construct($context, $data);
 $this->customQuoteItemsFactory = $customQuoteItems;
 $this->customQuoteFactory = $customQuote;
		 $this->productFactory = $productFactory;
		$this->customerSession = $customerSession;

 
    }
    protected function _prepareLayout()
    {
	 
			$this->pageConfig->getTitle()->set(__('My Quotes'));
		 
        
        parent::_prepareLayout();
        $page_size = $this->getPagerCount();
        $page_data = $this->getCustomData();
        if ($this->getCustomData()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'custom.pager.name'
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
	
	
	public function getQuoteItems($quoteId){
		        $collection = $this->customQuoteItemsFactory->create()->getCollection()->addFieldToFilter('quote_id',$quoteId);
 	  
         return $collection;

		
	}
	
	
	public function getProduct($productId){
		        $collection = $this->productFactory->create()->load($productId);
 	  
         return $collection;

		
	}
	
    public function getCustomData()
    {
        // get param values
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5; // set minimum records
        // get custom collection
		
 		
		  $customer_id = $this->customerSession->getCustomer()->getId();
        $collection = $this->customQuoteFactory->create()->getCollection()->addFieldToFilter('customer_id',$customer_id);;
		$collection->setPageSize($pageSize);
        $collection->setCurPage($page);
 		$collection->setOrder('id','Desc');
	  
         return $collection;
    }

    public function getPagerCount()
    {
        // get collection
        $minimum_show = 5; // set minimum records
        $page_array = [];
        $list_data = $this->customQuoteFactory->create()->getCollection();
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