<?php namespace Mtwo\Quote\Helper;
    use Magento\Customer\Model\Session;

    class MailHelper extends \Magento\Framework\App\Helper\AbstractHelper {

		const XML_PATH_EMAIL_DEMO = 'emaildemo/email/email_demo_template';
		protected $_inlineTranslation;
		protected $_transportBuilder;
		protected $_template;
		protected $_storeManager;
    
		public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
			\Mtwo\Quote\Model\TransportBuilder $transportBuilder,
			\Magento\Store\Model\StoreManagerInterface $storeManager
		)  
		{
 			parent::__construct($context);
			$this->_inlineTranslation = $inlineTranslation;
			$this->_transportBuilder = $transportBuilder;
			$this->_storeManager = $storeManager;
		}
    
		public function sendMail($quote_id,$customer,$filename)
		{
			
			$pdfFile = 'https://boffinbutler.com/pdf/quote/request/quote_id/'.$quote_id.'/';
			$name = $customer->getFirstname()." ".$customer->getLastname();
			$email = $customer->getEmail();
 			$emailTemplateVariables['QUOTE_ID'] = $quote_id;
			$emailTemplateVariables['name'] = $name;
 			$this->_template =  16;
 			$this->_inlineTranslation->suspend();

			$this->_transportBuilder
				->setTemplateIdentifier($this->_template)
				->setTemplateOptions(
					[
					'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
					'store' => $this->_storeManager->getStore()->getId(),
					]
				)
 				->setTemplateVars($emailTemplateVariables)
				->setFrom([
				'name' => 'Boffinbutler',
				'email' => 'info@boffinbutler.com',
				])
				->addTo($email, $name)
			 	->addAttachment(file_get_contents($pdfFile),$filename)
				;
 
			try {
				$transport = $this->_transportBuilder->getTransport();
				$transport->sendMessage();
				$this->_inlineTranslation->resume();
			} catch (\Exception $e) {
				echo $e->getMessage(); die;
			}
		}
}
