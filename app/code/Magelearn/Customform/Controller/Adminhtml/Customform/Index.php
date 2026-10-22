<?php
declare(strict_types=1);

namespace Magelearn\Customform\Controller\Adminhtml\Customform;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Magelearn::top_level');
		$resultPage->addBreadcrumb(__('Customform'), __('Customform'));
        $resultPage->addBreadcrumb(__('Manage Enqueries'), __('Manage Enqueries'));
            $resultPage->getConfig()->getTitle()->prepend(__("Manage Enqueries"));
            return $resultPage;
    }
}

