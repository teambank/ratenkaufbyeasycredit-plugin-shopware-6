<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Netzkollektiv\EasyCreditApi\Client\HttpClientFactory;
use Netzkollektiv\EasyCreditApi\Merchant;
use Psr\Log\LoggerInterface;

class MerchantFactory
{
    protected $settings;

    protected $logger;

    public function __construct(
        SettingsServiceInterface $settingsService,
        LoggerInterface $logger
    ) {
        $this->settings = $settingsService;
        $this->logger = $logger;
    }

    public function create(?\Shopware\Core\System\SalesChannel\SalesChannelContext $salesChannelContext = null, bool $validateSettings = true): Merchant
    {
        $salesChannelId = null;
        if ($salesChannelContext) {
            $salesChannelId = $salesChannelContext->getSalesChannel()->getId();
        }
        $settings = $this->settings->getSettings($salesChannelId, $validateSettings);

        $logger = new Logger(
            $this->logger,
            $settings
        );
        $config = new Config(
            $settings
        );
        $clientFactory = new HttpClientFactory();

        return new Merchant(
            $config,
            $clientFactory,
            $logger
        );
    }
}
