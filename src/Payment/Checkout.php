<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Payment;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Checkout implements EventSubscriberInterface
{
    public function __construct(
        PaymentHelper $paymentHelper,
        CheckoutFactory $checkoutFactory,
        Storage $storage,
        TagAwareAdapterInterface $cache,
        int $expirationTime
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->checkoutFactory = $checkoutFactory;
        $this->storage = $storage;
        $this->cache = $cache;
        $this->expirationTime = $expirationTime;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfirmLoaded',
        ];
    }

    /**
     * @throws InconsistentCriteriaIdsException
     */
    public function onCheckoutConfirmLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();
        /*if (!$this->paymentMethodUtil->isPaypalPaymentMethodInSalesChannel($salesChannelContext)) {
            return;
        }*/
        $checkout = $this->checkoutFactory->create($salesChannelContext);

        $cacheItem = $this->cache->getItem('easycredit-agreement');
        if ($cacheItem->isHit() && $cacheItem->get()) {
            $agreement = $cacheItem->get();
        } else {
            $agreement = $checkout->getAgreement();

            $cacheItem->set($agreement);
            $cacheItem->expiresAfter($this->expirationTime);
            $this->cache->save($cacheItem);
        }

        $event->getPage()->addExtension('easycredit', (new CheckoutData())->assign([
            'paymentMethodId' => $this->paymentHelper->getPaymentMethodId($salesChannelContext->getContext()),
            'agreement' => $agreement,
            'paymentPlan' => $this->storage->get('payment_plan'),
        ]));
    }
}
