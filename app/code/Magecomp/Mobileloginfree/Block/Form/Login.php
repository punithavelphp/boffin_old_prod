<?php
namespace Magecomp\Mobileloginfree\Block\Form;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magecomp\Mobileloginfree\Helper\Data as HelperData;


class Login extends \Magento\Customer\Block\Form\Login
{
    private $helperData;

    public function __construct(Context $context,Session $customerSession,Url $customerUrl,
        HelperData $helperData,array $data = []) {
        parent::__construct($context, $customerSession, $customerUrl, $data);
        $this->helperData = $helperData;
    }

    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }

    public function getMode()
    {
        switch ($this->helperData->getLoginMode()) {
            case 1:
                $mode = $this->AllowedMobileNumber();
                break;
            case 2:
                $mode = $this->AllowedMobileNumberAndEmail();
                break;
        }
        return $this->addData($mode);
    }

    private function AllowedMobileNumber()
    {
        return [
            'note' => $this->escapeHtml(
                __('If you have an account, sign in with your phone number.')
            ),
            'label' => $this->escapeHtml(__('Mobile Number')),
            'title' => $this->escapeHtmlAttr(__('Mobile Number'))
        ];
    }

    private function AllowedMobileNumberAndEmail()
    {
        return [
            'note' => $this->escapeHtml(
                __('If you have an account, sign in with your email address or phone number.')
            ),
            'label' => $this->escapeHtml(__('Email Address or Mobile Number')),
            'title' => $this->escapeHtmlAttr(__('Email or Mobile'))
        ];
    }
}
