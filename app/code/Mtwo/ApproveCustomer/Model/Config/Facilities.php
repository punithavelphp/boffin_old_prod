<?php 

namespace Mtwo\ApproveCustomer\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Used in creating options for getting product type value.
 */
class Facilities extends AbstractSource
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
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
		
		
		 $datas = $this->customerCollection->addAttributeToSelect(array('entity_id','firstname','lastname'))
			   ->addAttributeToFilter('group_id',array('in'=>array(2,3)))
			//  ->addAttributeToFilter('is_approved','approved')
			 ;
		
	 
		
 		$options[] = [
                'value' => '',
                'label' => 'Please Select'
            ];
		
        foreach ($datas as $key => $data) {
			
            $options[] = [
                'value' => $data->getId(),
                'label' => $data->getFirstname().' '.$data->getLastname()
            ];
        }
         return $options;
    }

    /**
     * @return array
     */
	
	public function getAllOptions()
	{
		return $this->toOptionArray();
	}

	public function getOptionText($value)
{
    foreach ($this->getAllOptions() as $option) {
        if ($option['value'] == $value) {
            return $option['label'];
        }
    }
    return false;
}

	 
    public function toArray()
    {
		 foreach ($this->getAllOptions() as $option) {
			//if ($option['value'] == $value) {
			//	return $option['label'];
			//}
			$arr[$option['value']] = $option['label'];
    	}
		return $arr;
        return [
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
            self::NOTAPPROVE => __('Not Approved'),
        ];
    }  
}
