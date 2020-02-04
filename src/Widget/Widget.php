<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Widget;

use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Widget implements EventSubscriberInterface
{
    private $settings;

    private $cartService;

    public function __construct(
        SettingsServiceInterface $settingsService,
        CartService $cartService
    ) {
        $this->settings = $settingsService;
        $this->cartService = $cartService;
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

        $settings = $this->settings->getSettings($context->getSalesChannel()->getId());

        if (!$settings->getWidgetEnabled()) {
            return;
        }

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorProductDetail(),
        ]));
    }

    public function onCartPageLoaded(CheckoutCartPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        $cart = $this->cartService->getCart($context->getToken(), $context);
        $settings = $this->settings->getSettings($context->getSalesChannel()->getId());

        if (!$settings->getWidgetEnabled()) {
            return;
        }

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorCart(),
            'amount' => $cart->getPrice()->getTotalPrice(),
        ]));
    }

    public function onOffcanvasCartPageLoaded(OffcanvasCartPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        $cart = $this->cartService->getCart($context->getToken(), $context);
        $settings = $this->settings->getSettings($context->getSalesChannel()->getId());

        if (!$settings->getWidgetEnabled()) {
            return;
        }

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorCart(),
            'amount' => $cart->getPrice()->getTotalPrice(),
        ]));
    }
}
