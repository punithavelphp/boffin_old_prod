<?php

namespace Magelearn\Customform\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magelearn\Customform\Model\CustomformFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Ves\Brand\Model\Brand;
class Save extends \Magento\Framework\App\Action\Action
{
    public $brand;

	/**
     * @var Customform
     */
    protected $_customform;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    public function __construct(
		Context $context,
        CustomformFactory $customform,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
		Brand $brand
    ) {
        $this->_customform = $customform;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
		$this->brand = $brand;
        parent::__construct($context);
    }
	public function execute()
    {
    	if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        //$data = $this->getRequest()->getParams();
        $data = $this->validatedParams();
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
           try{
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png','pdf','csv','xls','xlsx']);
                $imageAdapter = $this->adapterFactory->create();
            //    $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $uploaderFactory->setAllowCreateFolders(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('magelearn/customform');
                $result = $uploaderFactory->save($destinationPath);
				 
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                
                $imagePath = 'magelearn/customform'.$result['file'];
			   
                $data['image'] = $imagePath;
            } catch (\Exception $e) {
           }
        }
    	$customform = $this->_customform->create();
        $customform->setData($data);
        if($customform->save()){
			
			//trigger mail to admin
			
      
 $om = \Magento\Framework\App\ObjectManager::getInstance();
$messageInterfaceFactory = $om->get('\Magento\Framework\Mail\MessageInterfaceFactory');
	$mailTransportFactory = 		$om->get('\Magento\Framework\Mail\TransportInterfaceFactory');
			
    $from = 'support@boffinbutler.com';
    $to = 'boffinbutlerltd@gmail.com';
		 	$cc = 'punithavelkt@gmail.com';
    $subject = "Inquiry Recieved";
    $emailHtml = "<h1>Inquiry Details</h1>";
	$emailHtml .= '<table border="1">';
			foreach($data as $datakey=>$dataValue){
				$keyname = strtoupper($datakey);
				if($keyname == 'FORM_KEY'){
					// donothing
				}else if($keyname == 'BRAND' && !empty($data['brand'])){
 					 $brandData = $this->brand->load($data['brand']);
					 $emailHtml .= '<tr><td>'.$keyname .' </td><td>'.$brandData->getData('name').'</td></tr>';
				}
				else if($keyname == 'IMAGE'){
					$emailHtml .= '<tr><td>Attachment</td><td>https://boffinbutler.com/media/'.$dataValue.'</td></tr>';
				}else{
					$emailHtml .= '<tr><td>'.$keyname .' </td><td>'.$dataValue.'</td></tr>';
				}
			}
			$emailHtml .= '</table>';
		
				
    try {
        $message = $messageInterfaceFactory->create();
        $message->setFromAddress($from);
        $message->addTo($to);
		$message->addCc($cc);
        $message->setSubject($subject);
        $message->setBodyHtml($emailHtml);
        $transport = $mailTransportFactory->create(['message' => $message]);
        $transport->sendMessage();

    } catch (\Exception $e) {
        echo "error=>".$e->getMessage();die;
    }

			
			
			
			
			
            $this->messageManager->addSuccessMessage(__('Thanks for contacting us. We will get back to you soon.'));
        }else{
            $this->messageManager->addErrorMessage(__('Some Error Occurs. Please try again.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customform');
        return $resultRedirect;
    }
/**
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('first_name')) === '') {
            throw new LocalizedException(__('Enter the First Name and try again.'));
        }
		if (trim($request->getParam('last_name')) === '') {
            throw new LocalizedException(__('Enter the Last Name and try again.'));
        }
		if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
		if (trim($request->getParam('phone')) === '') {
            throw new LocalizedException(__('Enter the Phone Number and try again.'));
        }
		if (trim($request->getParam('message')) === '') {
            throw new LocalizedException(__('Enter your message and try again.'));
        }
        return $request->getParams();
    }
}
