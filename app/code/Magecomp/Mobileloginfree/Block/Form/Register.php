<?php
namespace Magecomp\Mobileloginfree\Block\Form;

use Magento\Framework\View\Element\Template\Context;
use Magento\Directory\Helper\Data;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionFactory;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryFactory;
use Magento\Framework\Module\Manager;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magecomp\Mobileloginfree\Helper\Data as HelperData;

class Register extends \Magento\Customer\Block\Form\Register
{
    private $helperData;

    public function __construct(
        Context $context,
        Data $directoryHelper,
        EncoderInterface $jsonEncoder,
        Config $configCacheType,
        RegionFactory $regionCollectionFactory,
        CountryFactory $countryCollectionFactory,
        Manager $moduleManager,
        Session $customerSession,
        Url $customerUrl,
        HelperData $helperData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $moduleManager,
            $customerSession,
            $customerUrl,
            $data
        );
        $this->helperData = $helperData;
    }

    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }
}