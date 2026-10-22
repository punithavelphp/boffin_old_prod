<?php
namespace Mtwo\Productupload\Controller\Product;
use Magento\Framework\App\Action\Context;
 use Mtwo\ApproveCustomer\Controller\ActionProcessor;
use Mtwo\ApproveCustomer\Helper\Data;
use Mtwo\Productupload\Model\Hsnupdate as HsnupdateModel;
class Hsnupdate extends ActionProcessor
{
	protected $customerFactory;
	protected $helperData;
	
    public function __construct(
    	Context $context
 		,Data $helper
		,HsnupdateModel $hsnupdateModel
     ) {
 		$this->helper  = $helper;
		$this->hsnupdateModel  = $hsnupdateModel;
        parent::__construct($context);
    }
	
    public function execute()
    {
		if(isset($_POST['hsncodearray'])){
			$dataArr = $_POST['hsncodearray'];
			print_r($dataArr);
 			foreach($dataArr as $data){
			 	$this->hsnupdateModel->updateHsncode($data['id'],$data['hsncode']);
			}
			 return array('status'=>true);
		}
     }
}