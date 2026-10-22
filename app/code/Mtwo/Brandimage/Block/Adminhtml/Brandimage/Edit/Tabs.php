<?php
namespace Mtwo\Brandimage\Block\Adminhtml\Brandimage\Edit;

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
        $this->setId('brandimage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Brandimage Information'));
    }
}