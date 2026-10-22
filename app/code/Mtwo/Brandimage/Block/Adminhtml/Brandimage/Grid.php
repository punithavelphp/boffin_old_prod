<?php
namespace Mtwo\Brandimage\Block\Adminhtml\Brandimage;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	/**
     * @var \Magento\Framework\Module\Manager
     */
	protected $moduleManager;

	/**
     * @var \Mtwo\Brandimage\Model\brandimageFactory
     */
	protected $_brandimageFactory;

	/**
     * @var \Mtwo\Brandimage\Model\Status
     */
	protected $_status;

	/**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mtwo\Brandimage\Model\brandimageFactory $brandimageFactory
     * @param \Mtwo\Brandimage\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Mtwo\Brandimage\Model\BrandimageFactory $BrandimageFactory,
		\Mtwo\Brandimage\Model\Status $status,
		\Magento\Framework\Module\Manager $moduleManager,
		array $data = []
	) {
		$this->_brandimageFactory = $BrandimageFactory;
		$this->_status = $status;
		$this->moduleManager = $moduleManager;
		parent::__construct($context, $backendHelper, $data);
	}

	/**
     * @return void
     */
	protected function _construct()
	{
		parent::_construct();
		$this->setId('postGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
		$this->setVarNameFilter('post_filter');
	}

	/**
     * @return $this
     */
	protected function _prepareCollection()
	{
		$collection = $this->_brandimageFactory->create()->getCollection();
		$this->setCollection($collection);

		parent::_prepareCollection();

		return $this;
	}

	/**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
	protected function _prepareColumns()
	{
		$this->addColumn(
			'id',
			[
				'header' => __('ID'),
				'type' => 'number',
				'index' => 'id',
				'header_css_class' => 'col-id',
				'column_css_class' => 'col-id'
			]
		);




		$this->addColumn(
			'attribute_id',
			[
				'header' => __('Brand'),
				'index' => 'attribute_id',
				'type' => 'options',
				'options' => \Mtwo\Brandimage\Block\Adminhtml\Brandimage\Grid::getOptionArray1()
			]
		);





		//$this->addColumn(
		//'edit',
		//[
		//'header' => __('Edit'),
		//'type' => 'action',
		//'getter' => 'getId',
		//'actions' => [
		//[
		//'caption' => __('Edit'),
		//'url' => [
		//'base' => '*/*/edit'
		//],
		//'field' => 'id'
		//]
		//],
		//'filter' => false,
		//'sortable' => false,
		//'index' => 'stores',
		//'header_css_class' => 'col-action',
		//'column_css_class' => 'col-action'
		//]
		//);




		$block = $this->getLayout()->getBlock('grid.bottom.links');
		if ($block) {
			$this->setChild('grid.bottom.links', $block);
		}

		return parent::_prepareColumns();
	}



	/**
     * @return string
     */
	public function getGridUrl()
	{
		return $this->getUrl('brandimage/*/index', ['_current' => true]);
	}

	/**
     * @param \Mtwo\Brandimage\Model\brandimage|\Magento\Framework\Object $row
     * @return string
     */
	public function getRowUrl($row)
	{

		return $this->getUrl(
			'brandimage/*/edit',
			['id' => $row->getId()]
		);

	}


	static public function getOptionArray1()
	{
		$data_array=array(); 


		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$atrributesRepository  = $objectManager->create('\Magento\Catalog\Model\Product\Attribute\Repository');
		$selectOptions = $atrributesRepository->get('brand')->getOptions();

		foreach ($selectOptions as $selectOption) {
			 		$data_array[$selectOption->getData('value')]=$selectOption->getData('label');
 		}




		return($data_array);
	}
	static public function getValueArray1()
	{
		$data_array=array();
		foreach(\Mtwo\Brandimage\Block\Adminhtml\Brandimage\Grid::getOptionArray1() as $k=>$v){
			$data_array[]=array('value'=>$k,'label'=>$v);
		}
		return($data_array);

	}


}