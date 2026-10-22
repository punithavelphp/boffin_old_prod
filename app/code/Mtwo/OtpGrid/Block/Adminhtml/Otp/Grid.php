<?php
namespace Mtwo\OtpGrid\Block\Adminhtml\Otp;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Mtwo\OtpGrid\Model\otpFactory
     */
    protected $_otpFactory;

    /**
     * @var \Mtwo\OtpGrid\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mtwo\OtpGrid\Model\otpFactory $otpFactory
     * @param \Mtwo\OtpGrid\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Mtwo\OtpGrid\Model\OtpFactory $OtpFactory,
        \Mtwo\OtpGrid\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_otpFactory = $OtpFactory;
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
        $collection = $this->_otpFactory->create()->getCollection();
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
					'mobile_number',
					[
						'header' => __('Mobile'),
						'index' => 'mobile_number',
					]
				);
				
				$this->addColumn(
					'otp',
					[
						'header' => __('OTP'),
						'index' => 'otp',
					]
				);
				

						$this->addColumn(
							'status',
							[
								'header' => __('Expiry'),
								'index' => 'status',
								'type' => 'options',
								'options' => \Mtwo\OtpGrid\Block\Adminhtml\Otp\Grid::getOptionArray3()
							]
						);

						
				$this->addColumn(
					'created_at',
					[
						'header' => __('Created'),
						'index' => 'created_at',
						'type'      => 'datetime',
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
        return $this->getUrl('otpgrid/*/index', ['_current' => true]);
    }

    /**
     * @param \Mtwo\OtpGrid\Model\otp|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	
		static public function getOptionArray3()
		{
            $data_array=array(); 
			$data_array[0]='No';
			$data_array[1]='Yes';
            return($data_array);
		}
		static public function getValueArray3()
		{
            $data_array=array();
			foreach(\Mtwo\OtpGrid\Block\Adminhtml\Otp\Grid::getOptionArray3() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		

}