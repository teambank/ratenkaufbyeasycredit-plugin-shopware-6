<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Netzkollektiv\EasyCredit\Api\IntegrationFactory;
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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

class Redirector implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var IntegrationFactory
     */
    private $integrationFactory;

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

    private $logger;

    public function __construct(
        ContainerInterface $container,
        IntegrationFactory $integrationFactory,
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
        QuoteHelper $quoteHelper,
        PaymentHelper $paymentHelper,
        Storage $storage,
        LoggerInterface $logger
    ) {
        $this->container = $container;
        $this->integrationFactory = $integrationFactory;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->quoteHelper = $quoteHelper;
        $this->paymentHelper = $paymentHelper;
        $this->storage = $storage;
        $this->logger = $logger;
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

        if (version_compare($this->container->getParameter('kernel.shopware_version'), '6.4.0', '>=')
            && !$event->getRequestDataBag()->get('easycredit')
        ) {
            return;
        }

        $this->storage
            ->set('duration', $event->getRequestDataBag()->get('easycredit')->get('number-of-installments'))
            ->set('init', true);
    }

    public function onCheckoutConfirmLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        if (!$this->storage->get('init')) {
            return;
        }
        $this->storage->set('init', false);

        $salesChannelContext = $event->getSalesChannelContext();

        $checkout = $this->integrationFactory->createCheckout($salesChannelContext);
        $quote = $this->quoteHelper->getQuote($salesChannelContext);

        try {
            $checkout->isAvailable($quote);
            $checkout->start($quote);
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $this->storage->set('error', 'Es ist ein Fehler aufgetreten. Leider steht Ihnen ratenkauf by easyCredit derzeit nicht zur Verfügung.');
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
