<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Netzkollektiv\EasyCreditApi\Checkout;
use Netzkollektiv\EasyCreditApi\Client;
use Netzkollektiv\EasyCreditApi\Client\HttpClientFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class CheckoutFactory
{
    protected $settings;

    protected $logger;

    protected $session;

    public function __construct(
        SettingsServiceInterface $settingsService,
        LoggerInterface $logger,
        Session $session
    ) {
        $this->settings = $settingsService;
        $this->logger = $logger;
        $this->session = $session;
    }

    /**
     * @param \Shopware\Core\System\SalesChannel\SalesChannelContext|null $salesChannelContext
     */
    public function create(?\Shopware\Core\System\SalesChannel\SalesChannelContext $salesChannelContext = null, bool $validateSettings = true): Checkout
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

        $client = new Client(
            $config,
            $clientFactory,
            $logger
        );
        $storage = new Storage(
            $this->session
        );

        return new Checkout(
            $client,
            $storage
        );
    }
}
