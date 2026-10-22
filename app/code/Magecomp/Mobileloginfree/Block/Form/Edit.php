<?php
namespace Magecomp\Mobileloginfree\Block\Form;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Newsletter\Model\SubscriberFactory;
use Magecomp\Mobileloginfree\Setup\InstallData;
use Magecomp\Mobileloginfree\Helper\Data as HelperData;

class Edit extends \Magento\Customer\Block\Form\Edit
{

    private $helperData;

    public function __construct(
        Context $context,
        Session $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        HelperData $helperData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->helperData = $helperData;
    }

    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }

    public function getMobilenumber()
    {
        $phoneAttribute = $this->getCustomer()
            ->getCustomAttribute(InstallData::MOBILENUMBER);
        return $phoneAttribute ? (string) $phoneAttribute->getValue() : '';
    }
}