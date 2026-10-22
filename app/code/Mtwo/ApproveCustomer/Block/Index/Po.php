<?php

namespace Mtwo\ApproveCustomer\Block\Index;

use Magento\Framework\View\Element\Template;

class Po extends Template
{
    protected $customerFactory;
    protected $customerCollection;

    public function __construct(
        Template\Context $context
        ,\Magento\Customer\Model\CustomerFactory $customerFactory // Add your custom Model
        ,\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection // Add your custom Model
		,\Magento\Customer\Model\Session $customerSession
		,\Magento\Customer\Model\ResourceModel\Group\Collection $group
        ,array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerFactory = $customerFactory;
        $this->customerCollection = $customerCollection;
		$this->customerSession = $customerSession;
				$this->group = $group;

    }
	public function getCustomerGroups() {
 		 $groups = $this->group
			 		->addFieldToSelect(array('customer_group_id','customer_group_code'))
			 		->addFieldToFilter('customer_group_id',array('neq'=>0));
		foreach($groups as $group){
 			$groupArray[$group->getData('customer_group_id')] = $group->getData('customer_group_code');	
		}
		return $groupArray;
	}
	
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Purchase Departments List'));
        parent::_prepareLayout();
        $page_size = $this->getPagerCount();
        $page_data = $this->getCustomData();
        if ($this->getCustomData()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'customer.pager.name'
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
		$faculty_attr = 165;
		$subquery = new \Zend_Db_Expr("(SELECT DISTINCT entity_id as fac_cus_id from customer_entity_int where `value` = '".$customer_id."' and attribute_id = '".$faculty_attr."')");
 
		
        $collection = $this->customerFactory->create()->getCollection()->addAttributeToSelect("*");
		$group_id = $this->customerSession->getCustomer()->getGroupId();
		 
			$collection->addFieldToFilter('group_id',array('in'=>array(6)));
		 
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
		$collection->setOrder('created_at','desc');
		$collection->getSelect()->join( array( 't' => $subquery ), 'e.entity_id = t.fac_cus_id', array());
		$collection->load();
        return $collection;
    }

    public function getPagerCount()
    {
        // get collection
        $minimum_show = 5; // set minimum records
        $page_array = [];
        $list_data = $this->customerCollection->create();
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