<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mtwo\Coins\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Mtwo\Coins\Model\CatalogFactory $catalogFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
		$this->catalogFactory = $catalogFactory;
		$this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }
	
	public function getCoinProducts(){
		
		return $this->catalogFactory->create()->getCollection()->addFieldToFilter('status','1');
	}
	
	
	public function getMediaUrl()
	{
		return $mediaUrl = $this->storeManager
						 ->getStore()
						 ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

	}

	
	
}

