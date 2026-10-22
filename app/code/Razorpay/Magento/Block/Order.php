<?php

namespace Razorpay\Magento\Block;
 class Order extends \Magento\Framework\View\Element\Template {

    protected $coreSession;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context
    , \Magento\Framework\Session\SessionManagerInterface $coreSession
    , \Magento\Sales\Model\OrderFactory $orderFactory
    , array $data = []
    ) {
        $this->coreSession = $coreSession;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getOrderDetails($orderId) {
        return $this->orderFactory->create()->loadByIncrementId($orderId);
    }

    public function getPageSession() {
       # return $this->setValue('');
    }

    public function setPageSession($session) {
        return $this->setValue($session);
    }

    public function unsetPageSession($session) {
        return $this->unSetValue();
    }

    public function setValue($message) {
        $this->_coreSession->start();
        $this->_coreSession->setMessage($message);
    }

    public function getValue() {
        $this->_coreSession->start();
        return $this->_coreSession->getMessage();
    }

    public function unSetValue() {
        $this->_coreSession->start();
        return $this->_coreSession->unsMessage();
    }

}
