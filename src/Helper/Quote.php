<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Api;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Netzkollektiv\EasyCreditApi\Rest\QuoteInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RequestStack;

class Quote
{
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        MetaDataProvider $metaDataProvider,
        SettingsServiceInterface $settingsService,
        Api\Storage $storage
    ) {
        $this->requestStack = $requestStack;
        $this->metaDataProvider = $metaDataProvider;
        $this->settingsService = $settingsService;
        $this->storage = $storage;
    }

    /**
     * @param Cart|\Shopware\Core\Checkout\Order\OrderEntity|null $cart
     */
    public function getQuote($cart, SalesChannelContext $salesChannelContext): QuoteInterface
    {
        if ($cart instanceof Cart) {
            return new Api\Quote(
                $cart,
                $this->metaDataProvider,
                $salesChannelContext,
                $this->settingsService,
                $this->storage
            );
        }

        return new Api\Order(
            $cart,
            $this->metaDataProvider,
            $salesChannelContext
        );
    }
}
