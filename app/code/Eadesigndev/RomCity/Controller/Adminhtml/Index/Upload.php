<?php
/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

namespace Eadesigndev\RomCity\Controller\Adminhtml\Index;

use Eadesigndev\RomCity\Helper\Data;
use Eadesigndev\RomCity\Model\RomCityRepository;
use Eadesigndev\RomCity\Model\RomCityFactory;
use Eadesigndev\RomCity\Model\CustomCityFactory as CityFactory;
use Eadesigndev\RomCity\Model\ResourceModel\Collection\Collection;
use Eadesigndev\RomCity\Model\ResourceModel\Collection\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends Action
{
    public $cityFactory;
    public $_fileUploaderFactory;
    public $_ioAdapter;
    public $_logger;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Eadesigndev_RomCity::romcity';

    protected $resultPageFactory;

    private $csvProccesor;

    private $moduleReader;

    private $directoryList;

    private $dataHelper;

    private $collectionFactory;

    private $romCityFactory;

    private $resultRedirect;

    private $romCityRepository;

    public function __construct(
        Context $context,
        Csv $csvProccesor,
        Reader $moduleReader,
        PageFactory $resultPageFactory,
        DirectoryList $directoryList,
        CollectionFactory $collectionFactory,
        ResultFactory $resultRedirect,
        RomCityFactory $romCityFactory,
		        cityFactory $cityFactory,

        RomCityRepository $romCityRepository,
        Data $dataHelper 
		    , \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    , \Psr\Log\LoggerInterface $logger
    , \Magento\Framework\Filesystem\Io\File $ioAdapter
    ) {
        $this->romCityRepository = $romCityRepository;
        $this->resultRedirect = $resultRedirect;
        $this->romCityFactory = $romCityFactory;
		        $this->cityFactory = $cityFactory;

        $this->collectionFactory = $collectionFactory;
        $this->dataHelper = $dataHelper;
        $this->directoryList = $directoryList;
        $this->moduleReader = $moduleReader;
        $this->csvProccesor = $csvProccesor;
        $this->resultPageFactory = $resultPageFactory;

		        $this->_fileUploaderFactory = $fileUploaderFactory;
 
        $this->_ioAdapter = $ioAdapter;
         $this->_logger = $logger;

		
		
        parent::__construct($context);
    }

    /**
     * Index action list city.
     * @return $resultRedirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_redirect->getRefererUrl();
        $resultRedirect->setUrl($url);

        $this->readCsv();

        return $resultRedirect;
    }

    public function readCsv()
    {
        $pubMediaDir = $this->directoryList->getPath(DirectoryList::MEDIA);
    //    $fieName = $this->dataHelper->getConfigFileName();
		        $ds = DIRECTORY_SEPARATOR;
          $dirTest = '/test';
 		 $target = $pubMediaDir . $dirTest . $ds;
 		$uploader = $this->_fileUploaderFactory->create(['fileId' => 'update_customercsv_file']);
		$uploader->setAllowedExtensions(['csv']);
		$uploader->setAllowRenameFiles(true);
		$save_file_name = date('Y-m-d-H-i-s') . '.csv';
		$result = $uploader->save($target, $save_file_name);
		$uploaded_file_name = $uploader->getUploadedFileName();
		
		 
        $file = $pubMediaDir . $dirTest . $ds . $uploaded_file_name;
  
        if (!empty($file)) {
            $csvData = $this->csvProccesor->getData($file);

            $csvDataProcessed = [];
            unset($csvData[0]);
            list($collection, $csvDataProcessed) = $this->csvProcessValues($csvData, $csvDataProcessed);

            foreach ($csvDataProcessed as $dataRow) {
                $regionId = $dataRow['region_id'];
                $cityName = $dataRow['city'];
                $entityId = $dataRow['entity_id'];

                $romCityRepository = $this->romCityFactory->create();
             //   if (isset($entityId) && is_numeric($entityId)) {
                    $romCityCollection = $this->collectionFactory->create()
						->addFieldToFilter('region_id',$regionId)
						->addFieldToFilter('city',$cityName);
					if($romCityCollection->getSize()){
				 
                    continue;
                 }else{
					  $this->cityFactory ->create()->setRegionId($regionId)
                 		->setCity($cityName)->save();
					}

 
               // $collection->addItem($romCityRepository);
            }
        }
       // $collection->walk('save');
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * @param $csvData
     * @param $csvDataProcessed
     * @return array
     */
    private function csvProcessValues($csvData, $csvDataProcessed)
    {
        /** @var  Collection $collection */
        $collection = $this->collectionFactory->create();

        foreach ($csvData as $csvValue) {
            $csvValueProcessed = [];
            foreach ($csvValue as $key => $value) {
                if ($key == 0) {
                    $csvValueProcessed['entity_id'] = $value;
                }

                if ($key == 1) {
                    $csvValueProcessed['region_id'] = $value;
                }

                if ($key == 2) {
                    $csvValueProcessed['city'] = $value;
                }
            }
            $csvDataProcessed[] = $csvValueProcessed;
        }
        return [$collection, $csvDataProcessed];
    }
}


