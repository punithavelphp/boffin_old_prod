<?php
namespace Mtwo\InstituteGst\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Mtwo\InstituteGst\Model\ResourceModel\InstituteGst\CollectionFactory as InstituteGstCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;

class Index extends Template
{
    protected $customerCollectionFactory;
    protected $instituteGstCollectionFactory;
    protected $attributeRepository;

    public function __construct(
        Context $context,
        CustomerCollectionFactory $customerCollectionFactory,
        InstituteGstCollectionFactory $instituteGstCollectionFactory,
        AttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->instituteGstCollectionFactory = $instituteGstCollectionFactory;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($context, $data);
    }

    public function getInstituteData()
    {
        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addAttributeToSelect('institute');
        $customerCollection->addAttributeToFilter('institute', ['notnull' => true]);
        $customerCollection->addAttributeToFilter('institute', ['neq' => '']);
        
        // Get institute attribute to get option labels
        $instituteAttribute = $this->attributeRepository->get(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            'institute'
        );
        
        $institutes = [];
        foreach ($customerCollection as $customer) {
            $instituteId = $customer->getInstitute();
            $instituteName = $this->getAttributeOptionLabel($instituteAttribute, $instituteId);
            if(strtolower($instituteName) == "others"){
				continue;
			}
            if ($instituteName && !isset($institutes[$instituteId])) {
                $institutes[$instituteId] = [
                    'name' => $instituteName,
                    'id' => $instituteId,
                    'gst_number' => ''
                ];
            }
        }

        $gstCollection = $this->instituteGstCollectionFactory->create();
        $existingGst = [];
        foreach ($gstCollection as $gstRecord) {
            $existingGst[$gstRecord->getInstituteId()] = $gstRecord->getGstNumber();
        }

        foreach ($institutes as $key => $institute) {
            if (isset($existingGst[$institute['id']])) {
                $institutes[$key]['gst_number'] = $existingGst[$institute['id']];
            }
        }

        return $institutes;
    }

    /**
     * Get attribute option label by value
     */
    protected function getAttributeOptionLabel($attribute, $optionId)
    {
        $options = $attribute->getOptions();
        foreach ($options as $option) {
            if ($option->getValue() == $optionId) {
                return $option->getLabel();
            }
        }
        return $optionId; // fallback to ID if label not found
    }

    public function getSaveRowUrl()
    {
        return $this->getUrl('*/*/saveRow');
    }

    /**
     * Get form key for CSRF protection
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}