<?php 

namespace Mtwo\CustomAddress\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\CustomerApproval\Helper\Data as HelperData;
use Magento\Customer\Api\AddressRepositoryInterface;

/**
 * Class AddressSave
 *
  */

class AddressSave implements ObserverInterface
{
    /**
     * @var HelperData
     */
    private $helperData;
protected $_resourceConnection;
		protected $_connection;

    /**
      *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData, AddressRepositoryInterface $addressRepository,\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->helperData = $helperData;
		        $this->addressRepository = $addressRepository;
			$this->_resourceConnection = $resourceConnection;
			$this->_connection = $this->_resourceConnection->getConnection();

    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
          
         $customerAddress     = $observer->getEvent()->getCustomerAddress();
  
 
        $addressId      = $customerAddress->getId();
         if ($addressId) {
                      // $addressData = $this->addressRepository->getById($addressId);
			 
			
			 $connection = $this->_connection;
			$tableName =  'customer_address_entity';
			 $sql = "update  " . $tableName . "  set is_active = '2' where entity_id = '".$addressId."'"; 
			// $connection->query($sql);
		 
			 
			 
			 
  //  $addressData->setData('is_active',2); // Set as Request for Approval - Status
   //  $this->addressRepository->save($addressData);
             
        }
    }
}
