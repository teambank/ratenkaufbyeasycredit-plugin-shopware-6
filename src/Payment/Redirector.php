<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Payment;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextSwitchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Redirector implements EventSubscriberInterface
{
    public function __construct(
        CheckoutFactory $checkoutFactory,
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
        QuoteHelper $quoteHelper,
        PaymentHelper $paymentHelper
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->quoteHelper = $quoteHelper;
        $this->paymentHelper = $paymentHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            SalesChannelContextSwitchEvent::class => 'onSalesChannelContextSwitch',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onSalesChannelContextSwitch(SalesChannelContextSwitchEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();

        if ($this->request->attributes->get('_route') !== 'frontend.checkout.configure') {
            return;
        }
        if (!$this->paymentHelper->isSelected($salesChannelContext, $event->getRequestDataBag()->get('paymentMethodId'))) {
            return;
        }

        $quote = $this->quoteHelper->getQuote($salesChannelContext);

        try {
            $checkout = $this->checkoutFactory->create($salesChannelContext)->start(
                $quote,
                $this->router->generate('frontend.easycredit.cancel', [], UrlGeneratorInterface::ABSOLUTE_URL), // cancel
                $this->router->generate('frontend.easycredit.return', [], UrlGeneratorInterface::ABSOLUTE_URL), // return
                $this->router->generate('frontend.easycredit.reject', [], UrlGeneratorInterface::ABSOLUTE_URL) // reject
            );
            $this->request->attributes->set('easycredit_redirect', $checkout->getRedirectUrl());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $attributes = $this->request->attributes;
        if ($redirectUrl = $attributes->get('easycredit_redirect')) {
            $event->setResponse(new RedirectResponse($redirectUrl));
            $attributes->set('easycredit_redirect', null);
        }
    }
}
