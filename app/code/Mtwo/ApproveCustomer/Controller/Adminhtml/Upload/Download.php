<?php

namespace Mtwo\ApproveCustomer\Controller\Adminhtml\Upload;
use Magento\Framework\App\Filesystem\DirectoryList;
class Download extends \Magento\Backend\App\Action {

    protected $resultPageFactory;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory
		,DirectoryList $directoryList
		,        \Magento\Framework\App\Response\Http\FileFactory $fileFactory

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_directoryList = $directoryList;
		        $this->fileFactory = $fileFactory;

    }

    public function execute() {
		$csv = $this->getRequest()->getParam('file',false);
		
		$file_path = $this->_directoryList->getPath(DirectoryList::MEDIA);
        $folderPath = $file_path . "/customerupload/";
		if($csv) {
		//	$this->downloadCsv($folderPath.''.$csv.'.csv');exit;
		
		
		$filepath  = 'customerupload/'.$csv.'.csv';
		$downloadedFileName = $csv.'.csv';
         $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = 0;
        return $this->fileFactory->create($downloadedFileName, $content, DirectoryList::MEDIA);
		}
		
		
		
        return $resultPage = $this->resultPageFactory->create();
    }

   public function downloadCsv($file) {



        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
          //  ob_clean();
            //flush();
            readfile($file);
        }
	   exit;
    }

}
