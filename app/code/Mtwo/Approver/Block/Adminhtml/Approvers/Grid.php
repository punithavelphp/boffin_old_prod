<?php
namespace Mtwo\Approver\Block\Adminhtml\Approvers;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Mtwo\Approver\Model\approversFactory
     */
    protected $_approversFactory;

    /**
     * @var \Mtwo\Approver\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mtwo\Approver\Model\approversFactory $approversFactory
     * @param \Mtwo\Approver\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Mtwo\Approver\Model\ApproversFactory $ApproversFactory,
        \Mtwo\Approver\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_approversFactory = $ApproversFactory;
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
        $collection = $this->_approversFactory->create()->getCollection();
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
							'employer_id',
							[
								'header' => __('employer Id'),
								'index' => 'employer_id',
								'type' => 'options',
								'options' => \Mtwo\Approver\Block\Adminhtml\Approvers\Grid::getOptionArray1()
							]
						);

						

						$this->addColumn(
							'employee_id',
							[
								'header' => __('Employee Id'),
								'index' => 'employee_id',
								'type' => 'options',
								'options' => \Mtwo\Approver\Block\Adminhtml\Approvers\Grid::getOptionArray2()
							]
						);

						


		

		

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
        return $this->getUrl('approver/*/index', ['_current' => true]);
    }

    /**
     * @param \Mtwo\Approver\Model\approvers|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	
		static public function getOptionArray1()
		{
            $data_array=array(); 
			$data_array[0]='1';
            return($data_array);
		}
		static public function getValueArray1()
		{
            $data_array=array();
			foreach(\Mtwo\Approver\Block\Adminhtml\Approvers\Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='2';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(\Mtwo\Approver\Block\Adminhtml\Approvers\Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		

}