<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Helper;

use Symfony\Component\HttpFoundation\RequestStack;

use Shopware\Core\Framework\Context;
use Shopware\Core\PlatformRequest;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;

use Netzkollektiv\EasyCredit\Api;


class Quote
{
    /**
     * @var EntityRepositoryInterface
     */
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        CartService $cartService
    )
    {
        $this->requestStack = $requestStack;
        $this->cartService = $cartService;
    }

    protected function getSalesChannelContext() {
        return $this->requestStack
            ->getMasterRequest()
            ->attributes
            ->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
    }

    public function getQuote($context = null, $cart = null)
    {
        if (null === $context) {
            $context = $this->getSalesChannelContext();
        }
        if (null === $cart) {
            $cart = $this->cartService->getCart($context->getToken(), $context);
        }

        try {
            return new Api\Quote(
                $cart,
                $context
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}



