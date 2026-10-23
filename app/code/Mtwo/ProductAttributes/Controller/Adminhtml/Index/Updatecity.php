<?php 
namespace Mtwo\ProductAttributes\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Mtwo\ProductAttributes\Helper\Data;
class Updatecity extends \Magento\Backend\App\Action
{ 
    public $_coreRegistry;
    public $helper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
		Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
		$this->helper = $helper;
        parent::__construct($context);
    }

  
	
    public function execute(){
       
 		$this->helper->deleteAllCityAttribute();
		 $this->messageManager->addSuccess('City Attribute updated Success');
		  
	   return $this->resultRedirectFactory->create()->setPath(
                '*/*/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        return $resultPage;
    }
	
}