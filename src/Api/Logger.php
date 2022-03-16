<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Monolog\Logger as Monolog;
use Netzkollektiv\EasyCredit\Setting\SettingStruct;

class Logger implements \Netzkollektiv\EasyCreditApi\LoggerInterface
{
    /**
     * @var Monolog
     */
    protected $_logger;

    /**
     * @var bool
     */
    protected $debug = false;

    public function __construct(
        Monolog $logger,
        SettingStruct $settings
    ) {
        $this->_logger = $logger;

        if ($settings->getDebug()) {
            $this->debug = true;
            $this->allowLineBreaks(true);
        }
    }

    public function log($msg): self
    {
        $this->logInfo($msg);

        return $this;
    }

    public function logDebug($msg): self
    {
        if (!$this->debug) {
            return $this;
        }

        $this->_logger->info(
            $this->_format($msg)
        );

        return $this;
    }

    public function logInfo($msg): self
    {
        if (!$this->debug) {
            return $this;
        }

        $this->_logger->info(
            $this->_format($msg)
        );

        return $this;
    }

    public function logWarn($msg): self
    {
        $this->_logger->warning(
            $this->_format($msg)
        );

        return $this;
    }

    public function logError($msg): self
    {
        $this->_logger->error(
            $this->_format($msg)
        );

        return $this;
    }

    public function _format($msg): string
    {
        if (\is_array($msg) || \is_object($msg)) {
            $msg = \print_r($msg, true);
        }

        return $msg;
    }

    /**
     * @param true $bool
     */
    protected function allowLineBreaks(bool $bool): void
    {
        $handlers = $this->_logger->getHandlers();
        if (
            \is_array($handlers)
            && isset($handlers[0])
            && $handlers[0] instanceof \Monolog\Handler\StreamHandler
            && $handlers[0]->getFormatter() instanceof \Monolog\Formatter\LineFormatter
        ) {
            $handlers[0]->getFormatter()->allowInlineLineBreaks($bool);
        }
    }
}
