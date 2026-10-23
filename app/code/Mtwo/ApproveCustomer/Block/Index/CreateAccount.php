<?php

namespace Mtwo\ApproveCustomer\Block\Index;
use Mtwo\ApproveCustomer\Helper\Data;
use Magento\Framework\View\Element\Template;

class CreateAccount extends Template
{
	public $helper;

	protected $customerCollection;

	public function __construct(
		Template\Context $context,
		Data $helper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->helper = $helper;

	} 

	protected function _prepareLayout()
	{
		$group_id = $this->getGroupId();
		if($group_id == 2){
 			if($this->getRequest()->getParam('group') == 'purchasedept'){
				if($this->getRequest()->getParam('customer') != ''){
					$this->pageConfig->getTitle()->set(__('Update Purchase Department'));
				}else{
					$this->pageConfig->getTitle()->set(__('Create Purchase Department'));
				}
			}else{
				if($this->getRequest()->getParam('customer') != ''){
					$this->pageConfig->getTitle()->set(__('Update Student'));	
				}else{
					$this->pageConfig->getTitle()->set(__('Create Student'));	
				}
			}

		}else if($group_id == 7){
			if($this->getRequest()->getParam('customer') != ''){
			$this->pageConfig->getTitle()->set(__('Update Faculty'));
			}else{
				$this->pageConfig->getTitle()->set(__('Create Faculty'));	
			}
		}
		else if($group_id == 3 || $group_id == 6){
			if($this->getRequest()->getParam('customer') != ''){
				$this->pageConfig->getTitle()->set(__('Update Employee'));
			}else{
				$this->pageConfig->getTitle()->set(__('Create Employee'));
			}
		}
		parent::_prepareLayout();
		return $this;
	}

	public function getFormData()
	{
		$data = $this->getData('form_data');
		if ($data === null) {
			$formData = $this->getCustomFormData(true);
			$data = new \Magento\Framework\DataObject();
			if ($formData) {
				$data->addData($formData);
				$data->setCustomerData(1);
			}
			$this->setData('form_data', $data);
		}

		return $this->getData('form_data');
	}


	public function getCustomFormData($true) {
		return $this->helper->getCustomFormData($true); 
	}


	public function getMaxApproverCount(){
		return $this->helper->getMaxApproverCount();
	}


	public function getApproverCount(){
		return $this->helper->getApproverCount();
	}
	public function getIsLoggedIn(){
		return $this->helper->getIsLoggedIn();
	}
	public function getGroupId(){
		return $this->helper->getGroupId();
	}


	public function getCustomerId(){
		return $this->helper->getCustomerId();
	}


	public function getCustomerDataById($id){
		return $this->helper->getCustomerDataById($id);
	}

	public function employeeApproverCollection($cusId = ''){
		return $this->helper->employeeApproverCollection($cusId);
	}


	public function getApproverCollection($cusId = ''){
		return $this->helper->getApproverCollection($cusId);
	}

	public function getApproverDetails($entity_array){
		return $this->helper->getApproverDetails($entity_array);
	}
}