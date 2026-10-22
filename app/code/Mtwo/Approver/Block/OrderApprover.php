<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mtwo\Approver\Block;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Mtwo\Approver\Model\Approved;
use Magento\Customer\Model\Customer;
/**
 * Invoice view  comments form
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class OrderApprover extends \Magento\Sales\Block\Order\Info
{
    

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
		Approved $approved,
		Customer $customer,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->paymentHelper = $paymentHelper;
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
		 $this->approved = $approved;
		 $this->customer = $customer;
        parent::__construct($context, $registry,$paymentHelper,$addressRenderer,$data);
    }
	
	public function getApproversForOrder(){
		  
		$order = $this->getOrder();	
		$approvedBy = $this->approved->getCollection()->addFieldToFilter('order_id',$order->getId())->setOrder('created_at','ASC');
		foreach($approvedBy as $aBy){
			$approvedByArray[] = $aBy->getManagerId();
			$approvedByDate[$aBy->getManagerId()] = $aBy;
		}
		if(isset($approvedByArray)){
			$customers = $this->customer->getCollection()->addFieldToFilter('entity_id',array('in'=>$approvedByArray));
			return array('customers'=>$customers,"approvedby"=>$approvedByDate);
		}
		return false;
		
	}
 
}
