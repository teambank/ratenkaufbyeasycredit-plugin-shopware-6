<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Widget;

use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Widget implements EventSubscriberInterface
{
    private $settings;

    private $cartService;

    private $paymentHelper;

    public function __construct(
        SettingsServiceInterface $settingsService,
        CartService $cartService,
        PaymentHelper $paymentHelper
    ) {
        $this->settings = $settingsService;
        $this->cartService = $cartService;
        $this->paymentHelper = $paymentHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            CheckoutCartPageLoadedEvent::class => 'onCartPageLoaded',
            //OffcanvasCartPageLoadedEvent::class => 'onOffcanvasCartPageLoaded'
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();
        $product = $event->getPage()->getProduct();

        $settings = $this->getSettings($context);
        if (!$settings) {
            return;
        }

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorProductDetail(),
            'widgetExtended' => $settings->getWidgetExtended(),
            'amount' => $product->getCalculatedPrice()->getUnitPrice(),
        ]));
    }

    public function onCartPageLoaded(CheckoutCartPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        $settings = $this->getSettings($context);
        if (!$settings) {
            return;
        }

        $cart = $this->cartService->getCart($context->getToken(), $context);

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorCart(),
            'widgetExtended' => $settings->getWidgetExtended(),
            'amount' => $cart->getPrice()->getTotalPrice(),
        ]));
    }

    public function onOffcanvasCartPageLoaded(OffcanvasCartPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        $settings = $this->getSettings($context);
        if (!$settings) {
            return;
        }

        $cart = $this->cartService->getCart($context->getToken(), $context);

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorCart(),
            'amount' => $cart->getPrice()->getTotalPrice(),
        ]));
    }

    protected function getSettings(SalesChannelContext $context)
    {
        if (!$this->paymentHelper->isPaymentMethodInSalesChannel($context)) {
            return false;
        }

        try {
            $settings = $this->settings->getSettings($context->getSalesChannel()->getId());
        } catch (SettingsInvalidException $e) {
            return false;
        }

        if (!$settings->getWidgetEnabled()) {
            return false;
        }

        return $settings;
    }
}
