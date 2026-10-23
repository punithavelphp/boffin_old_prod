<?php

namespace Mtwo\Quote\Controller\Adminhtml\quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
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
        $resultPage->setActiveMenu('Mtwo_Quote::quote');
        $resultPage->addBreadcrumb(__('Mtwo'), __('Mtwo'));
        $resultPage->addBreadcrumb(__('Manage item'), __('Manage Quote'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Quote'));

        return $resultPage;
    }
}
?>