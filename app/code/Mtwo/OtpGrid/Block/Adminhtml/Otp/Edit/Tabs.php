<?php
namespace Mtwo\OtpGrid\Block\Adminhtml\Otp\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('otp_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Otp Information'));
    }
}