<?php

namespace AL\CustomLog\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Log extends AbstractHelper {
    public $shipmentLogger;
    public $invoiceLogger;
    public $shipmentCancelLogger;
    public $errorLogger;


    /**
     * 
     * @var \AL\CustomLog\Logger\Logger
     */
    protected $dmsLogger;
    protected $paymentLogger;
    protected $payBySelfLogger;
    protected $returnUrlLogger;
	protected $payoutLogger;

    public function __construct(
	\AL\CustomLog\Logger\Logger $dmsLogger
	, \AL\CustomLog\Logger\PaymentLogger $paymentLogger
	, \AL\CustomLog\Logger\PayBySelfLogger $payBySelfLogger
	, \AL\CustomLog\Logger\ReturnUrlLogger $returnUrlLogger
	, \AL\CustomLog\Logger\ShipmentLogger $shipmentLogger
	, \AL\CustomLog\Logger\InvoiceLogger $invoiceLogger
	, \AL\CustomLog\Logger\PayoutLogger $payoutLogger
	, \AL\CustomLog\Logger\ShipmentCancelLogger $shipmentCancelLogger
	, \AL\CustomLog\Logger\ErrorLogger $errorLogger
	 
    ) {
        $this->dmsLogger = $dmsLogger;
        $this->paymentLogger = $paymentLogger;
        $this->payBySelfLogger = $payBySelfLogger;
        $this->returnUrlLogger = $returnUrlLogger;
		$this->shipmentLogger = $shipmentLogger;
		$this->invoiceLogger = $invoiceLogger;
		$this->payoutLogger = $payoutLogger;
		$this->shipmentCancelLogger = $shipmentCancelLogger;
		$this->errorLogger = $errorLogger;

    }

    public function dmsLog($message) {
        $this->dmsLogger->info($message);
    }
	public function shipmentLog($message) {
        $this->shipmentLogger->info($message);
    }
    public function paymentLog($message) {
        $this->paymentLogger->info($message);
    }

    public function payBySelfLogger($message) {
        $this->payBySelfLogger->info($message);
    }

    public function returnUrlLog($message) {
        $this->returnUrlLogger->info($message);
    }
	public function invoiceLog($message) {
        $this->invoiceLogger->info($message);
    }
	public function payoutLog($message) {
        $this->payoutLogger->info($message);
    }
	public function shipmentCancelLog($message) {
        $this->shipmentCancelLogger->info($message);
    }
	public function errorLog($message) {
        $this->errorLogger->debug($message);
    }
	
 
}
