<?php
namespace Mtwo\RefrensInvoice\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Invoicedate extends AbstractRenderer
{
    public function render(DataObject $row)
    {
		 
		if($this->getColumn()->getIndex() == 'refrens_pdf_url') {
			$pdf = $row->getData($this->getColumn()->getIndex());
			if ($pdf) {
				// Customize the date format as needed
 				return '<a target="_blank" href="'.$pdf.'">DOWNLOAD</a>';
			}
		}
		else if($this->getColumn()->getIndex() == 'billing_name') {
			$incID = $row->getData('magento_increment_id');
			$orderId = $row->getData('id');
 			#$order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($ID);
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			 $invoice = $objectManager->create(\Magento\Sales\Model\Order\InvoiceFactory::class)->create()->loadByIncrementId($incID);
			$order = $objectManager->create('\Magento\Sales\Model\Order')->load($invoice->getOrderId()); 
			#return $order;
			$firstName = $order->getCustomerFirstname();
			$lastName = $order->getCustomerLastname();
			return $firstName.' - '.$lastName;
		} 
		else if($this->getColumn()->getIndex() == 'increment_id') {
			$incID = $row->getData($this->getColumn()->getIndex());
			if(isset($this->incrementarray[$incID])){
				
			}
			return $incID;
			
		
		} 
		else{
			$date = $row->getData($this->getColumn()->getIndex());
			if ($date) {
				// Customize the date format as needed
				$formattedDate = date('d M Y', strtotime($date));
				return $formattedDate;
			}
		}
        return '';
    }
}
