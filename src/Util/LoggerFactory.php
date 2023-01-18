<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Util;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\Log\LoggerFactory as ShopwareLoggerFactory;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsService;

class LoggerFactory extends ShopwareLoggerFactory
{
    private int $logLevel = Logger::WARNING;

    public function setLogLevel(SystemConfigService $systemConfigService): void
    {
        $this->logLevel = Logger::WARNING;
        try {
            $isDebug = $systemConfigService->getBool(SettingsService::SYSTEM_CONFIG_DOMAIN.'debug');
            if ($isDebug) {
                $this->logLevel = Logger::DEBUG;
            }
        } catch (\Throwable $e) {

        }
    }

    public function createRotating(string $filePrefix, ?int $fileRotationCount = null, int $loggerLevel = Logger::DEBUG): LoggerInterface
    {
        $loggerLevel = $this->logLevel;
	return parent::createRotating($filePrefix, $fileRotationCount, $loggerLevel);
    }
}
