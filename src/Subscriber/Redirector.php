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
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextSwitchEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;
use Teambank\RatenkaufByEasyCreditApiV3\Integration\ValidationException;
use Teambank\RatenkaufByEasyCreditApiV3\ApiException;

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

    private $cartService;

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
        CartService $cartService,
        QuoteHelper $quoteHelper,
        PaymentHelper $paymentHelper,
        Storage $storage,
        LoggerInterface $logger
    ) {
        $this->container = $container;
        $this->integrationFactory = $integrationFactory;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->cartService = $cartService;
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
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onSalesChannelContextSwitch(SalesChannelContextSwitchEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();
        $attributes = (isset($this->request->attributes)) ? $this->request->attributes : null;

        if (!$this->isRoute('frontend.checkout.configure', $this->request)) {
            return;
        }

        if (!$event->getRequestDataBag()->get('paymentMethodId')
            || !$this->paymentHelper->isSelected($salesChannelContext, $event->getRequestDataBag()->get('paymentMethodId'))
        ) {
            return;
        }

        if (\version_compare($this->container->getParameter('kernel.shopware_version'), '6.4.0', '>=')
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
        $cart = $this->cartService->getCart($salesChannelContext->getToken(), $salesChannelContext);
        $quote = $this->quoteHelper->getQuote($cart, $salesChannelContext);
        try {
            try {
                $checkout->isAvailable($quote);
                $checkout->start($quote);
            } catch (ValidationException $e) {
                $this->storage->set('error',$e->getMessage());
            } catch (ApiException $e) {
                $response = json_decode($e->getResponseBody());
                if ($response === null || !isset($response->violations)) {
                    throw new \Exception('violations could not be parsed');
                }
                $messages = [];
                foreach ($response->violations as $violation) {
                    $messages[] = $violation->message;
                }
                $this->logger->warning($e);
                $this->storage->set('error', implode(' ',$messages));
            }
        } catch (\Throwable $e) {
            $this->logger->error($e);
            $this->storage->set('error', 'Es ist ein Fehler aufgetreten. Leider steht Ihnen easyCredit-Ratenkauf derzeit nicht zur Verfügung.');
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

    protected function isRoute($route, $request)
    {
        $attributes = (isset($request->attributes)) ? $request->attributes : null;

        if ($attributes === null
            || $attributes->get('_route') !== $route
        ) {
            return false;
        }

        return true;
    }
}
