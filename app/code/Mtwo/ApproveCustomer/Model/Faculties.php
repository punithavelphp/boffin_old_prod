<?php
namespace Mtwo\ApproveCustomer\Model;

use Mtwo\ApproveCustomer\Api\FacultiesInterface;

class Faculties implements FacultiesInterface
{
	protected $_scopeConfigure;
	protected $_logger;
	protected $customerCollection;
    public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigure,
		\Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection,
		\Psr\Log\LoggerInterface $logger
    )
    {
		$this->_scopeConfigure = $scopeConfigure;
		$this->_logger=$logger;
		$this->customerCollection = $customerCollection;

    }
	
	/**
     * Return  promotionalbanners details
     * @return mixed $result
     */
	
	public function getFaculties($institute_id) {
		   $data = $this->customerCollection
				   ->addAttributeToSelect(array('entity_id','firstname','lastname'))
				   ->addAttributeToFilter('group_id',2)
				   ->addAttributeToFilter('institute',$institute_id)
				//   ->addAttributeToFilter('is_approved','approved')
				   ;
  		 if($data->getSize()){
			 return $data->getData();
		 }else{
			 return array();
		 }
		
	}
	 
}
