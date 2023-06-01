<?php
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Util;

use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\RequestStack;
use Shopware\Core\PlatformRequest;

class LoggerConfigurator
{
    private SettingsServiceInterface $settings;

    protected $requestStack;

    public function __construct(
        SettingsServiceInterface $settingsService,
        RequestStack $requestStack
    ) {
        $this->settings = $settingsService;
        $this->requestStack = $requestStack;
    }

    public function configure($logger): void
    {

        $request = $this->requestStack->getCurrentRequest();
        $salesChannelId = null;
        if ($request && $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID)) {
            $salesChannelId = $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID);
        }

        $debug = $this->settings->getSettings($salesChannelId)->getDebug();
        
        foreach ($logger->getHandlers() as $handler) {
            $handler->setLevel($debug ? LogLevel::DEBUG : LogLevel::ERROR);
        }
    }
}