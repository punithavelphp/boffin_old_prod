<?php
namespace Mtwo\InstituteGst\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Mtwo\InstituteGst\Model\ResourceModel\InstituteGst\CollectionFactory as InstituteGstCollectionFactory;

class InstituteGst extends AbstractHelper
{
    protected $instituteGstCollectionFactory;

    public function __construct(
        Context $context,
        InstituteGstCollectionFactory $instituteGstCollectionFactory
    ) {
        $this->instituteGstCollectionFactory = $instituteGstCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Get GST number for a specific institute ID
     *
     * @param int $instituteId
     * @return string
     */
    public function getGstNumberById($instituteId)
    {
        $gstCollection = $this->instituteGstCollectionFactory->create();
        $gstCollection->addFieldToFilter('institute_id', $instituteId);
        $gstRecord = $gstCollection->getFirstItem();
        
        return $gstRecord->getId() ? $gstRecord->getGstNumber() : '';
    }
}
