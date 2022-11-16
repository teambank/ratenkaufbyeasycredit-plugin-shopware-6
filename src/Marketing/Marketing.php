<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Marketing;

use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Shopware\Core\Framework\Struct\ArrayEntity;

class Marketing implements EventSubscriberInterface
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
            //OffcanvasCartPageLoadedEvent::class => 'onOffcanvasCartPageLoaded',
            GenericPageLoadedEvent::class => 'onPageLoaded',
            NavigationPageLoadedEvent::class => 'onNavigationPageLoaded',
            SearchPageLoadedEvent::class => 'onSearchPageLoaded',
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

        if (!$settings->getWidgetEnabled()) {
            return;
        }

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorProductDetail(),
            'widgetExtended' => $settings->getWidgetExtended(),
            'widgetDisplayType' => $settings->getWidgetDisplayType(),
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

        if (!$settings->getWidgetEnabled()) {
            return;
        }

        $cart = $this->cartService->getCart($context->getToken(), $context);

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorCart(),
            'widgetExtended' => $settings->getWidgetExtended(),
            'widgetDisplayType' => $settings->getWidgetDisplayType(),
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

        if (!$settings->getWidgetEnabled()) {
            return;
        }

        $cart = $this->cartService->getCart($context->getToken(), $context);

        $event->getPage()->addExtension('easycredit', (new WidgetData())->assign([
            'apiKey' => $settings->getWebshopId(),
            'widgetSelector' => $settings->getWidgetSelectorCart(),
            'widgetDisplayType' => $settings->getWidgetDisplayType(),
            'amount' => $cart->getPrice()->getTotalPrice(),
        ]));
    }

    public function onPageLoaded(GenericPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

	    $settings = $this->getSettings($context);
        if (!$settings) {
            return;
        }

        $modalIsOpen = 'true';
        if ( intval( $settings->getModalSettingsDelay() ) > 0 ) {
            $modalIsOpen = 'false';
        }

        $event->getPage()->addExtension('easycredit', (new ArrayEntity())->assign([
            'modal' => $settings->getModalEnabled(),
            'modalIsOpen' => $modalIsOpen,
            'modalSettingsDelay' => $settings->getModalSettingsDelay(),
            'modalSettingsSnoozeFor' => $settings->getModalSettingsSnoozeFor(),
            'modalSettingsMedia' => $settings->getModalSettingsMedia(),
            'flashbox' => $settings->getFlashboxEnabled(),
            'flashboxSettingsMedia' => $settings->getFlashboxSettingsMedia(),
            'bar' => $settings->getBarEnabled(),
        ]));
    }

    public function onNavigationPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        $settings = $this->getSettings($context);
        if (!$settings) {
            return;
        }

        $event->getPage()->addExtension('easycreditCard', (new ArrayEntity())->assign([
            'card' => $settings->getCardEnabled(),
            'cardSettingsPosition' => $settings->getCardSettingsPosition(),
            'cardSettingsMedia' => $settings->getCardSettingsMedia(),
        ]));
    }

    public function onSearchPageLoaded(SearchPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        $settings = $this->getSettings($context);
        if (!$settings) {
            return;
        }

        $event->getPage()->addExtension('easycreditCard', (new ArrayEntity())->assign([
            'card' => $settings->getCardSearchEnabled(),
            'cardSettingsPosition' => $settings->getCardSettingsPosition(),
            'cardSettingsMedia' => $settings->getCardSettingsMedia(),
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

        return $settings;
    }
}
