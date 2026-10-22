<?php

namespace Magelearn\Customform\Ui\Component\Listing\Column;
use Magento\Framework\Escaper;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class HTMLLink extends Column{
    protected $escaper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource){
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
$mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
 

		
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
				if(!empty($item['image'])){
					$html = '<a href="'.$mediaUrl.$item['image'].'" target="_blank">'.basename($item['image']).'</a>';
					$item[$fieldName] = $this->escaper->escapeHtml($html, ['a']);
				}
            }
        }
        return $dataSource;
    }

}

