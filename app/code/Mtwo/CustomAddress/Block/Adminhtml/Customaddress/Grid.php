<?php
namespace Mtwo\CustomAddress\Block\Adminhtml\Customaddress;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Mtwo\CustomAddress\Model\customaddressFactory
     */
    protected $_customaddressFactory;

    /**
     * @var \Mtwo\CustomAddress\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mtwo\CustomAddress\Model\customaddressFactory $customaddressFactory
     * @param \Mtwo\CustomAddress\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Mtwo\CustomAddress\Model\CustomaddressFactory $CustomaddressFactory,
        \Mtwo\CustomAddress\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_customaddressFactory = $CustomaddressFactory;
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
        $this->setDefaultSort('entity_id');
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
        $collection = $this->_customaddressFactory->create()->getCollection()->addFieldToFilter('is_active',2);;
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
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
				$this->addColumn(
					'parent_id',
					[
						'header' => __('Customer ID'),
						'index' => 'parent_id',
					]
				);
					$this->addColumn(
					'firstname',
					[
						'header' => __('firstname'),
						'index' => 'firstname',
					]
				);
				
				$this->addColumn(
					'lastname',
					[
						'header' => __('lastname'),
						'index' => 'lastname',
					]
				);
				

						$this->addColumn(
							'city',
							[
								'header' => __('City'),
								'index' => 'city',
								'type' => 'options',
								'options' => \Mtwo\CustomAddress\Block\Adminhtml\Customaddress\Grid::getOptionArray2()
							]
						);

						

						$this->addColumn(
							'region',
							[
								'header' => __('Region'),
								'index' => 'region',
								'type' => 'options',
								'options' => \Mtwo\CustomAddress\Block\Adminhtml\Customaddress\Grid::getOptionArray3()
							]
						);

						
				$this->addColumn(
					'street',
					[
						'header' => __('Street'),
						'index' => 'street',
					]
				);
				 
			
				$this->addColumn(
					'postcode',
					[
						'header' => __('postcode'),
						'index' => 'postcode',
					]
				);
				 

				$this->addColumn(
					'is_active',
					[
						'header' => __('Is Active'),
						'index' => 'is_active',
						'type' => 'options',
						'options' => \Mtwo\CustomAddress\Block\Adminhtml\Customaddress\Grid::getOptionArray11()
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
                        //'field' => 'entity_id'
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
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('entity_id');
        //$this->getMassactionBlock()->setTemplate('Mtwo_CustomAddress::customaddress/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('customaddress');

         

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('customaddress/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customaddress/*/index', ['_current' => true]);
    }

    /**
     * @param \Mtwo\CustomAddress\Model\customaddress|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return  'javascriopt:void(0)';
		
    }

	
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='Trichy';
			$data_array[1]='Chennai';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(\Mtwo\CustomAddress\Block\Adminhtml\Customaddress\Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		
		static public function getOptionArray3()
		{
            $data_array=array(); 
			$data_array[0]='TN';
            return($data_array);
		}
		static public function getValueArray3()
		{
            $data_array=array();
			foreach(\Mtwo\CustomAddress\Block\Adminhtml\Customaddress\Grid::getOptionArray3() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		
		static public function getOptionArray11()
		{
            $data_array=array(); 
			$data_array[0]='Reject';
			$data_array[1]='Approve';
			$data_array[2]='New';
             return($data_array);
		}
		static public function getValueArray11()
		{
            $data_array=array();
			foreach(\Mtwo\CustomAddress\Block\Adminhtml\Customaddress\Grid::getOptionArray11() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		

}