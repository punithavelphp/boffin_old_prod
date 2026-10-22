<?php
namespace Mtwo\Quote\Block\Adminhtml\Quote;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Mtwo\Quote\Model\quoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Mtwo\Quote\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mtwo\Quote\Model\quoteFactory $quoteFactory
     * @param \Mtwo\Quote\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Mtwo\Quote\Model\QuoteFactory $QuoteFactory,
        \Mtwo\Quote\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_quoteFactory = $QuoteFactory;
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
        $collection = $this->_quoteFactory->create()->getCollection();
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
            'customer_id',
            [
                'header' => __('Customer'),
                'type' => 'text',
                'index' => 'customer_id',
                'header_css_class' => 'job-state-header',
                'column_css_class' => 'job-state-column',
				'renderer'  => 'Mtwo\Quote\Block\Adminhtml\Renderer\Customer'
            ]
        );
		 
		 
		 $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'type' => 'datetime',
                'index' => 'created_date',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
   
 		     $this->addColumn('edit', ['header' => __('View'), 'type' => 'action', 'getter' => 'getId', 'actions' => [['caption' => __('View'), 'url' => ['base' => 'pdf/quote/request'], 'field' => 'id','target'=>'_blank']], 'filter' => false, 'sortable' => false, 'index' => 'stores', 'header_css_class' => 'col-action', 'column_css_class' => 'col-action']);

 
		

		

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
        return $this->getUrl('quote/*/index', ['_current' => true]);
    }

    /**
     * @param \Mtwo\Quote\Model\quote|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	

}