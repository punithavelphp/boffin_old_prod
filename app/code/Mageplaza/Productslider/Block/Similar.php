<?php

/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Productslider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Productslider\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Mageplaza\Productslider\Helper\Data;

/**
 * Class CategoryId
 * @package Mageplaza\Productslider\Block
 */
class Similar extends AbstractSlider
{
	/**
	 * @var CategoryFactory
	 */
	protected $_categoryFactory;

	protected $productsList;
	/**
	 * CategoryId constructor.
	 *
	 * @param Context $context
	 * @param CollectionFactory $productCollectionFactory
	 * @param Visibility $catalogProductVisibility
	 * @param DateTime $dateTime
	 * @param Data $helperData
	 * @param HttpContext $httpContext
	 * @param EncoderInterface $urlEncoder
	 * @param CategoryFactory $categoryFactory
	 * @param Grouped $grouped
	 * @param Configurable $configurable
	 * @param LayoutFactory $layoutFactory
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		CollectionFactory $productCollectionFactory,
		Visibility $catalogProductVisibility,
		DateTime $dateTime,
		Data $helperData,
		HttpContext $httpContext,
		EncoderInterface $urlEncoder,
		CategoryFactory $categoryFactory,
		Grouped $grouped,
		Configurable $configurable,
		LayoutFactory $layoutFactory,
		array $data = []
	) {
		$this->_categoryFactory = $categoryFactory;

		parent::__construct(
			$context,
			$productCollectionFactory,
			$catalogProductVisibility,
			$dateTime,
			$helperData,
			$httpContext,
			$urlEncoder,
			$grouped,
			$configurable,
			$layoutFactory,
			$data
		);
	}

	public function getProductList()
	{

		return $this->productsList;
	}

	/**
	 * Get Product Collection by Category Ids
	 *
	 * @return $this|array
	 */
	public function getProductCollection()
	{

		$productIds = $this->getProductIdsByProductCode();
		$collection = [];
		if (!empty($productIds)) {
			$collection = $this->_productCollectionFactory->create()
				->addIdFilter(array('in' => $productIds));;
			$this->_addProductAttributesAndPrices($collection);
		}
		//	echo $collection->getSelect();
		//	echo $collection->getSize();die;
		$this->productsList = $collection;
		return $collection;
	}

	/**
	 * Get ProductIds by Category
	 *
	 * @return array
	 */
	public function getProductIdsByProductCode()
	{
		$productIds = [];


		$skip = true;
		$product_code = $this->getProduct()->getProductcode();
		$casnumber = $this->getProduct()->getCasnumber();

		$collection = $this->_productCollectionFactory->create();
		// $collection->addAttributeToSelect('*')->addAttributeToFilter('productcode',$product_code);
		//$collection->addAttributeToSelect('*')->addAttributeToFilter('casnumber',$casnumber);

		if (!empty($casnumber) && !empty($product_code)) {
			
				
			$collection->addAttributeToFilter(
    array(
        array('attribute' => 'casnumber', 'eq' => $casnumber),
        array('attribute' => 'productcode', 'eq' => $product_code)
     )
);
		/*	$collection->addFieldToFilter(
				['casnumber', 'productcode'],
				[
					['eq' => $casnumber],
					['eq' => $product_code]
				]
			); */
		} else if (!empty($casnumber)) {
			$collection->addAttributeToSelect('*')->addAttributeToFilter('casnumber', $casnumber);
		} else if (!empty($product_code)) {
			$collection->addAttributeToSelect('*')->addAttributeToFilter('productcode', $product_code);
		} else {
			$collection->addAttributeToSelect('*')->addAttributeToFilter('sku', $this->getProduct()->getSku());
		}
		
		foreach ($collection as $item) {
			$productIds[] = $item->getData('entity_id');
		}



		$keys = array_keys($productIds);
		shuffle($keys);
		$productIdsRandom = [];

		foreach ($keys as $key => $value) {
			$productIdsRandom[] = $productIds[$value];
			//    if ($key >= ($this->getProductsCount() - 1)) {

			if ($key >= 9) {
				break;
			}
		}

		return $productIdsRandom;
	}
}
