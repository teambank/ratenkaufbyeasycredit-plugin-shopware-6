<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Api;
use Netzkollektiv\EasyCreditApi\Rest\QuoteInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RequestStack;

class Quote
{
    private $requestStack;

    private $cartService;

    public function __construct(
        RequestStack $requestStack,
        CartService $cartService
    ) {
        $this->requestStack = $requestStack;
        $this->cartService = $cartService;
    }

    public function getQuote(SalesChannelContext $salesChannelContext, $cart = null): QuoteInterface
    {
        if ($cart === null) {
            $cart = $this->cartService->getCart($salesChannelContext->getToken(), $salesChannelContext);
        }
        if ($cart instanceof Cart) {
            return new Api\Quote(
                $cart,
                $salesChannelContext
            );
        }

        return new Api\Order(
                $cart
            );
    }
}
