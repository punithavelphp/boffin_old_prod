<?php

namespace Eadesigndev\RomCity\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Create extends \Magento\Backend\App\Action
{
    public $resultPageFactory;

    /**
     * @var PageFactory
     */
    protected $resultPagee;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Eadesigndev_RomCity::Create');
        $resultPage->addBreadcrumb(__('Region'), __('City'));
        $resultPage->addBreadcrumb(__('Manage item'), __('Manage City'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage City'));

        return $resultPage;
    }
}
?>