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
use Netzkollektiv\EasyCredit\Api\QuoteInvalidException;
use Netzkollektiv\EasyCredit\Api\QuoteBuilder;
use Netzkollektiv\EasyCredit\Api\OrderBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

use Teambank\RatenkaufByEasyCreditApiV3\Integration\TransactionInitRequestWrapper;

class Quote
{
    private $cartService;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(
        CartService $cartService,
        QuoteBuilder $quoteBuilder,
        OrderBuilder $orderBuilder
    ) {
        $this->cartService = $cartService;
        $this->quoteBuilder = $quoteBuilder;
        $this->orderBuilder = $orderBuilder;
    }

    /**
     * @param Cart|\Shopware\Core\Checkout\Order\OrderEntity|null $cart
     */
    public function getQuote($cart, SalesChannelContext $salesChannelContext): QuoteInterface
    {
        if ($cart instanceof Cart) {
            return $this->quoteBuilder->build($cart, $salesChannelContext);
        }
        return $this->orderBuilder->build($cart, $salesChannelContext);
    }
}
