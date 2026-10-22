<?php
namespace Mtwo\RefrensInvoice\Block\Adminhtml\Invoice;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Mtwo\RefrensInvoice\Model\invoiceFactory
     */
    protected $_invoiceFactory;

    /**
     * @var \Mtwo\RefrensInvoice\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mtwo\RefrensInvoice\Model\invoiceFactory $invoiceFactory
     * @param \Mtwo\RefrensInvoice\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Mtwo\RefrensInvoice\Model\InvoiceFactory $InvoiceFactory,
        \Mtwo\RefrensInvoice\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_invoiceFactory = $InvoiceFactory;
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
        $collection = $this->_invoiceFactory->create()->getCollection();
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
					'magento_increment_id',
					[
						'header' => __('Magento IN.No'),
						'index' => 'magento_increment_id',
					]
				);
		
				$this->addColumn(
					'increment_id',
					[
						'header' => __('InvoiceNumber'),
						'index' => 'increment_id',
						'renderer' => 'Mtwo\RefrensInvoice\Block\Adminhtml\Grid\Renderer\Invoicedate',
					]
				);
		
		$this->addColumn(
			'billing_name',
			[
				'header' => __('Billing Name'),
				'index' => 'billing_name',
				'renderer' => 'Mtwo\RefrensInvoice\Block\Adminhtml\Grid\Renderer\Invoicedate',
			]
		);

		
				$this->addColumn(
					'created_at',
					[
						'header' => __('Date'),
						'index' => 'created_at',
						 'renderer' => 'Mtwo\RefrensInvoice\Block\Adminhtml\Grid\Renderer\Invoicedate',

					]
				);
			 
				$this->addColumn(
					'base_grand_total',
					[
						'header' => __('Grand Total'),
						'index' => 'base_grand_total',
					]
				);
				$this->addColumn(
					'refrens_pdf_url',
					[
						'header' => __('PDF'),
						'index' => 'refrens_pdf_url',
						'renderer' => 'Mtwo\RefrensInvoice\Block\Adminhtml\Grid\Renderer\Invoicedate',
					]
				);
				
				
			

		

		
		   $this->addExportType($this->getUrl('refrensinvoice/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('refrensinvoice/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        return $this->getUrl('refrensinvoice/*/index', ['_current' => true]);
    }

    /**
     * @param \Mtwo\RefrensInvoice\Model\invoice|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	

}