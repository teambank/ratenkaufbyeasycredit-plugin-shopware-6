<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextSwitchEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Redirector implements EventSubscriberInterface
{
    /**
     * @var CheckoutFactory
     */
    private $checkoutFactory;

    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $request;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var QuoteHelper
     */
    private $quoteHelper;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(
        CheckoutFactory $checkoutFactory,
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
        QuoteHelper $quoteHelper,
        PaymentHelper $paymentHelper,
        Storage $storage
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->quoteHelper = $quoteHelper;
        $this->paymentHelper = $paymentHelper;
        $this->storage = $storage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SalesChannelContextSwitchEvent::class => 'onSalesChannelContextSwitch',
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfirmLoaded',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    protected function isRoute($route, $request) {
        $attributes = (isset($request->attributes)) ? $request->attributes : null;

        if ($attributes === null
            || $attributes->get('_route') !== $route
        ) {
            return false;
        }
        return true;
    }

    public function onSalesChannelContextSwitch(SalesChannelContextSwitchEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();
        $attributes = (isset($this->request->attributes)) ? $this->request->attributes : null;

        if (!$this->isRoute('frontend.checkout.configure', $this->request)) {
            return;
        }

        if (!$event->getRequestDataBag()->get('paymentMethodId') ||
            !$this->paymentHelper->isSelected($salesChannelContext, $event->getRequestDataBag()->get('paymentMethodId'))
        ) {
            return;
        }

        $this->storage->set('init', true);
    }


    public function onCheckoutConfirmLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        if (!$this->storage->get('init')) {
            return;
        }
        $this->storage->set('init', false);

        $salesChannelContext = $event->getSalesChannelContext();

        $checkout = $this->checkoutFactory->create($salesChannelContext);
        $quote = $this->quoteHelper->getQuote($salesChannelContext);

        try {
            $checkout->isAvailable($quote);
            $checkout->start(
                $quote,
                $this->router->generate('frontend.easycredit.cancel', [], UrlGeneratorInterface::ABSOLUTE_URL), // cancel
                $this->router->generate('frontend.easycredit.return', [], UrlGeneratorInterface::ABSOLUTE_URL), // return
                $this->router->generate('frontend.easycredit.reject', [], UrlGeneratorInterface::ABSOLUTE_URL) // reject
            );

            $this->storage->set('redirect_url', $checkout->getRedirectUrl());
        } catch (\Throwable $e) {

            $this->storage->set('error', $e->getMessage());
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->isRoute('frontend.checkout.confirm.page', $event->getRequest())) {
            return;
        }

        if ($redirectUrl = $this->storage->get('redirect_url')) {
            $event->setResponse(new RedirectResponse($redirectUrl));
            $this->storage->set('redirect_url', null);
        }
    }
}
