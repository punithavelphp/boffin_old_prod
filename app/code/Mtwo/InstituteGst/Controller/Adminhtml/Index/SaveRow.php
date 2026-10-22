<?php
namespace Mtwo\InstituteGst\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mtwo\InstituteGst\Model\InstituteGstFactory;
use Mtwo\InstituteGst\Model\ResourceModel\InstituteGst as InstituteGstResource;
use Magento\Framework\Controller\Result\JsonFactory;

class SaveRow extends Action
{
    protected $instituteGstFactory;
    protected $instituteGstResource;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        InstituteGstFactory $instituteGstFactory,
        InstituteGstResource $instituteGstResource,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->instituteGstFactory = $instituteGstFactory;
        $this->instituteGstResource = $instituteGstResource;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data || !isset($data['institute_id']) || !isset($data['gst_number'])) {
            return $result->setData([
                'success' => false,
                'message' => __('Invalid data provided.')
            ]);
        }

        try {
            $instituteId = (int)$data['institute_id'];
            $gstNumber = trim($data['gst_number']);

            $model = $this->instituteGstFactory->create();
            $this->instituteGstResource->load($model, $instituteId, 'institute_id');
            
            $model->setInstituteId($instituteId);
            $model->setGstNumber($gstNumber);
            
            $this->instituteGstResource->save($model);
            
            return $result->setData([
                'success' => true,
                'message' => __('GST number updated successfully.')
            ]);
            
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Error saving GST number: %1', $e->getMessage())
            ]);
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mtwo_InstituteGst::institute_gst');
    }
}
