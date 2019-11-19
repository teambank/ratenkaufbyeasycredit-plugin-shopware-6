<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Api;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\RequestStack;

class Quote
{
    /**
     * @var EntityRepositoryInterface
     */
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        CartService $cartService
    ) {
        $this->requestStack = $requestStack;
        $this->cartService = $cartService;
    }

    public function getQuote($context = null, $cart = null)
    {
        if ($context === null) {
            $context = $this->getSalesChannelContext();
        }
        if ($cart === null) {
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

    protected function getSalesChannelContext()
    {
        return $this->requestStack
            ->getMasterRequest()
            ->attributes
            ->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
    }
}
