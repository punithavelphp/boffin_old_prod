<?php
/**
		*Copyright©  All rights reserved.
		* See COPYING.txt for license details.
	*/
declare(strict_types=1);

namespace Mtwo\Coins\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;


class Order extends Action
{
	public $customerFactory;
	public $addressFactory;
	public $customerSession;
	public $jsonFactory;
	public $storeManager;
	public $coinsFactory;
	public $ordersFactory;
	public $catalogactory;
	public $_messageManager;


	/**
			* @var PageFactory
		*/
	protected $resultPageFactory;

	/**
			* Constructor
			*
			* @param PageFactory $resultPageFactory
		*/
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Model\AddressFactory $addressFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
		\Mtwo\Coins\Model\OrdersFactory $ordersFactory,
		\Mtwo\Coins\Model\CoinsFactory $coinsFactory,
		\Mtwo\Coins\Model\CatalogFactory $catalogFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager
	)
	{
		$this->customerFactory = $customerFactory;
		$this->addressFactory = $addressFactory;
		$this->customerSession = $customerSession;
		$this->resultPageFactory = $resultPageFactory;
		$this->jsonFactory = $jsonFactory;
		$this->storeManager = $storeManager;
		$this->coinsFactory = $coinsFactory;
		$this->ordersFactory = $ordersFactory;
		$this->catalogactory = $catalogFactory;

		parent::__construct($context);
		$this->_messageManager = $messageManager;
	}

	public function getCurrentCustomer()
	{
		return $this->customerSession->getCustomer();
	}

	/**
			* Execute view action
			*
			* @return ResultInterface
		*/
	public function execute()
	{
		//	$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

		//$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
		//$response->setHeader('Content-type', 'text/plain');

		// 

		$productId = $this->getRequest()->getPost('product_id');


		$customerAddressData['status']=false;
		$customerId  = $this->getCurrentCustomer()->getId();

		$customer = $this->customerFactory->create();
		$websiteId = $this->storeManager->getStore()->getWebsiteId();
		$customer->setWebsiteId($websiteId);
		$customerModel = $customer->load($customerId);

		$catalogData = $this->catalogactory->create()->load($productId);
		$requiredCoins = $catalogData->getCoins();

		$coinsCollection = $this->coinsFactory->create()->getCollection()
			->addFieldToFilter('customer_id',$customerId)
			->addFieldToSelect('customer_id')
			->addExpressionFieldToSelect('bb_coins', 'SUM({{coins}})', 'coins');
		$coinsCollection->getSelect()->group('customer_id');
		$coins = 0;
		if($coinsCollection->getSize())
		{
			$coins = $coinsCollection->getFirstItem()->getData('bb_coins');
		}


		if($coins > 0 && $requiredCoins > 0 && $coins >= $requiredCoins) {

			$customerAddressData = [];
			$customerAddress = [];

			if ($customerModel->getAddresses() != null) { 
				foreach ($customerModel->getAddresses() as $address) {
					$customerAddress[] = $address->toArray();
				}
			}
			if ($customerAddress != null) { 
				foreach ($customerAddress as $customerAddres) {
					$street = $customerAddres['street'];
					$city = $customerAddres['city'];
					$region=$customerAddres['region'];
					$country=$customerAddres['country_id'];
					$postcode=$customerAddres['postcode'];
					$address =$street;
					$address.= '<br>'.$city;
					$address.='<br>'.$region;
					$address.='<br>'.$postcode;
					$address.='<br>'.$country;

					// $customerAddressData[]=$customerAddres->toArray();
					$orderCreate = $this->ordersFactory->create()
						->setData('billing_address',$address)
						->setData('shipping_address',$address)
						->setData('customer_id',$customerModel->getId())
						->setData('customer_name',$customerModel->getName())
						->setData('customer_email',$customerModel->getEmail())
						->setData('catalog_id',$catalogData->getId())
						->setData('coins',$requiredCoins)
						->setData('quantity','1')->save()
						;
					
					$requiredCoins = $requiredCoins * -1;
					$this->coinsFactory->create()
						->setData('customer_id',$customerModel->getId())
						->setData('order_increment_id',$orderCreate->getId())
						->setData('code','REEDEM')
						->setData('comments','REEDEM')
						->setData('coins',$requiredCoins)
						->setData('is_approved',1)
						->setData('approved_by','SYSTEM')
 						->setData('is_debit','1')->save();
					break;
				}
			}
			$customerAddressData['status']=true;
			$customerAddressData['message']='Thanks for your shopping. You will recieve your order once we confirmed.';	
		//	$this->_messageManager->addSuccess(__("Thanks for your shopping. You will recieve your order once we confirmed"));
		}else{
			$customerAddressData['status']=false;
			$customerAddressData['message']='Invalid request.';				
		//	$this->_messageManager->addError(__("Invalid request."));


		}


return $this->jsonFactory->create()->setData($customerAddressData);

		// echo json_encode($customerAddressData);
	}
}