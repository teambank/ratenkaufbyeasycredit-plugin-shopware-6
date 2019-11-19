<?php
namespace Netzkollektiv\EasyCredit\Api;

use Psr\Log\LoggerInterface;
use Netzkollektiv\EasyCredit\Setting\SettingStruct;

class Logger implements \Netzkollektiv\EasyCreditApi\LoggerInterface {

    protected $_logger;

    protected $debug = false;

    public function __construct(
        LoggerInterface $logger,
        SettingStruct $settings
    ) {
        $this->_logger = $logger;

        //if ($settings->getDebug()) {
            $this->debug = true;
            $this->allowLineBreaks(true);
        //}
    }

    protected function allowLineBreaks($bool) {
        $handlers = $this->_logger->getHandlers();
        if (
            is_array($handlers)
            && isset($handlers[0])
            && $handlers[0] instanceof \Monolog\Handler\StreamHandler
            && $handlers[0]->getFormatter() instanceof \Monolog\Formatter\LineFormatter
        ) {
            $handlers[0]->getFormatter()->allowInlineLineBreaks($bool);
        }
    }

    public function log($msg) {
        $this->logInfo($msg);
        return $this;
    }

    public function logDebug($msg) {
        if (!$this->debug) {
            return;
        }

        $this->_logger->info(
            $this->_format($msg)
        );
        return $this;
    }

    public function logInfo($msg) {
        if (!$this->debug) {
            return;
        }

        $this->_logger->info(
            $this->_format($msg)
        );
        return $this;
    }

    public function logWarn($msg) {
        $this->_logger->warning(
            $this->_format($msg)
        );
        return $this;
    }

    public function logError($msg) {
        $this->_logger->error(
            $this->_format($msg)
        );
        return $this;
    }

    public function _format($msg) {
        if (is_array($msg) || is_object($msg)) {
            $msg = print_r($msg,true);
        }
        \mydebug($msg);
        return $msg;
    }
}