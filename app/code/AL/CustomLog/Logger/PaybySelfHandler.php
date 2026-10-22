<?php 
namespace AL\CustomLog\Logger;
 
use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
 
class PaybySelfHandler extends Base
{
    protected $loggerType = Logger::INFO;
 
    protected $fileName = '/var/log/paybyself.log';
}