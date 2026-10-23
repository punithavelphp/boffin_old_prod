<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Controller\Adminhtml\Catalog;

use Magento\Framework\Exception\LocalizedException;
use Mtwo\Coins\Model\ImageUploader;
class Save extends \Magento\Backend\App\Action
{
    public $imageUploaderModel;


    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
		ImageUploader $imageUploaderModel
    ) {
        $this->dataPersistor = $dataPersistor;
		 $this->imageUploaderModel = $imageUploaderModel;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('catalog_id');
        
            $model = $this->_objectManager->create(\Mtwo\Coins\Model\Catalog::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Catalog no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        	
           // try {
				 $this->imageData($model, $data);
			 	 
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Catalog.'));
                $this->dataPersistor->clear('mtwo_coins_catalog');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['catalog_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
          /*  } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Catalog.'));
            } */
        
            $this->dataPersistor->set('mtwo_coins_catalog', $data);
            return $resultRedirect->setPath('*/*/edit', ['catalog_id' => $this->getRequest()->getParam('catalog_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
	
	    public function imageData($model, $data)
    {
        if ($model->getId()) {
           // $pageData = $this->helloworldFactory->create();
			$pageData = $this->_objectManager->create(\Mtwo\Coins\Model\Catalog::class);
            $pageData->load($model->getId());
            if (isset($data['image'][0]['name'])) {
                $imageName1 = $pageData->getThumbnail();
                $imageName2 = $data['image'][0]['name'];
                if ($imageName1 != $imageName2) {
                    $imageUrl = $data['image'][0]['url'];
                    $imageName = $data['image'][0]['name'];
                    $data['image'] = $this->imageUploaderModel->saveMediaImage($imageName, $imageUrl);
                } else {
                    $data['image'] = $data['image'][0]['name'];
                }
            } else {
                $data['image'] = '';
            }
        } else {
            if (isset($data['image'][0]['name'])) {
                $imageUrl = $data['image'][0]['url'];
                $imageName = $data['image'][0]['name'];
                $data['image'] = $this->imageUploaderModel->saveMediaImage($imageName, $imageUrl);
            }
        }
        $model->setData($data);
        return $model;
    }

}

