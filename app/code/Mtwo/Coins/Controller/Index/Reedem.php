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


class Reedem extends Action
{

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
		$customerAddressData['status']=false;
		$customerId  = $this->getCurrentCustomer()->getId();
		$customerAddressData['customer_id']=$customerId;
		$customer = $this->customerFactory->create();
		$websiteId = $this->storeManager->getStore()->getWebsiteId();
		$customer->setWebsiteId($websiteId);
		$customerModel = $customer->load($customerId);

		$customerAddressData = [];
		$customerAddress = [];

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


		if ($coins > 0 && $customerModel->getAddresses() != null) { 
			foreach ($customerModel->getAddresses() as $address) {
				$customerAddress[] = $address->toArray();
			}
			if (count($customerAddress)) { 
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
					$customerAddressData['status']=true;
					$customerAddressData['address']=$address;
					// $customerAddressData[]=$customerAddres->toArray();
				}
			}
		}else{
			$customerAddressData['status']=false;
			if($coins <= 0){
				$customerAddressData['message']='You dont have enough coins to reedem';
				$this->_messageManager->addError(__("You dont have enough coins to reedem"));

			}else{
				$customerAddressData['message']='Some Error occurs. Please try again';
				$this->_messageManager->addError(__("Some Error occurs. Please try again"));

			}

		}

		return $this->jsonFactory->create()->setData($customerAddressData);
	}
}