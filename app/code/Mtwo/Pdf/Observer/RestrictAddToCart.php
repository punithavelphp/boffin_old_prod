<?php
namespace Mtwo\Pdf\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;

class RestrictAddToCart implements ObserverInterface
{
	public $messageManager;
	public $mhelper;
	public $sellerCity;
	public $customerFactory;
	public $request;

	protected $responseFactory;

	protected $url;

	private $scopeConfig;

	private $customerSession;

	private $customerUrl;

	private $context;

	private $contextHttp;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Http\Context $contextHttp,
		\Magento\Customer\Model\Url $customerUrl,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Magento\Framework\UrlInterface $url,
		Context $context,
		\Mtwo\Approver\Model\SellerCity $sellerCity,
		\Webkul\Marketplace\Helper\Data $data,
		CustomerFactory $customerFactory,
		\Magento\Framework\App\RequestInterface $request
	) {
		$this->scopeConfig     = $scopeConfig;
		$this->context         = $context;
		$this->customerUrl     = $customerUrl;
		$this->contextHttp     = $contextHttp;
		$this->messageManager = $context->getMessageManager();
		$this->responseFactory = $responseFactory;        
		$this->url = $url;
		$this->mhelper = $data;
		$this->sellerCity = $sellerCity;
		$this->customerFactory = $customerFactory;
		$this->request = $request;

	}

	public function getCustomerAddress($customerId)
	{
		$customer = $this->customerFactory->create();
		$customer->setWebsiteId(1);
		$customerModel = $customer->load($customerId);
		$customerAddressData = '';
		$customerAddress = [];
		if ($customerModel->getAddresses() != null)
		{
			foreach ($customerModel->getAddresses() as $address) {
				$customerAddress[] = $address->toArray();
			}
		}
		if ($customerAddress != null)
		{
			foreach ($customerAddress as $customerAddres)
			{
				$city = $customerAddres['city'];
				$customerAddressData = $city;
			}
		}
		return $customerAddressData;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		 
		$isLoggedIn = $this->contextHttp->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
		if($isLoggedIn){
			
		}else{
$this->request->setParam('product', false);
		throw new \Magento\Framework\Exception\LocalizedException(__('Please login to your account to add items to the cart.'));

		return $this;
		}
		
		$sellerId = $this->mhelper->getSellerIdByProductId($observer->getEvent()->getInfo('product'));
		$sellerCityData = $this->sellerCity->getCollection()->addFieldToFilter('seller_id',$sellerId);
		$sellerCityData->getSelect()
			->join(array('cityT' =>'eadesign_romcity'),'cityT.entity_id= city_id',
				   array(
					   'city' => 'city'
				   )
				  );
		$sellerId = $this->mhelper->getSellerIdByProductId($observer->getEvent()->getInfo('product'));
		$customerId = $this->contextHttp->getValue('customer_id');
		$customerCity = $this->getCustomerAddress($customerId);
		if($sellerCityData->getSize() && $customerCity != '') {
			$sellerCities = $sellerCityData->getData();



			foreach($sellerCities as $sellerCity){
				if(strtolower($sellerCity['city']) == strtolower($customerCity)){
					return $this;
				}
			}
		} 
		$this->messageManager->addError(__('We cannot deliver to your city. Try different Seller'));
		$this->request->setParam('product', false);
		throw new \Magento\Framework\Exception\LocalizedException(__('We cannot deliver to your city. Try different Seller'));

		return $this;

		//  return $this;
	}

}
