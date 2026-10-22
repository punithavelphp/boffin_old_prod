<?php
namespace Mtwo\Refrens\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Combine extends \Magento\Backend\App\Action
{
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
        $resultPage->setActiveMenu('Mtwo_Refrens::multiinvoice');
        $resultPage->addBreadcrumb(__('Multi'), __('Invoice'));
        $resultPage->addBreadcrumb(__('Multi Invoice'), __('Multi Invoice'));
        $resultPage->getConfig()->getTitle()->prepend(__('Multi Invoice'));

        return $resultPage;
    }
}
?>