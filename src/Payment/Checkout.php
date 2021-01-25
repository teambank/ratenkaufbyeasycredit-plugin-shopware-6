<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Payment;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Checkout implements EventSubscriberInterface
{
    private $paymentHelper;

    private $checkoutFactory;

    private $storage;

    private $cache;

    public function __construct(
        PaymentHelper $paymentHelper,
        SettingsServiceInterface $settingsService,
        CheckoutFactory $checkoutFactory,
        QuoteHelper $quoteHelper,
        Storage $storage,
        TagAwareAdapterInterface $cache
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->settings = $settingsService;
        $this->checkoutFactory = $checkoutFactory;
        $this->quoteHelper = $quoteHelper;
        $this->storage = $storage;
        $this->cache = $cache;
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
        if ($this->storage->get('redirect_url') 
            || $this->storage->get('init')
        ) {
            return;
        }

        $salesChannelContext = $event->getSalesChannelContext();
        $context = $event->getContext();
        $cart = $event->getPage()->getCart();

        if (!$this->paymentHelper->isPaymentMethodInSalesChannel($salesChannelContext)) {
            return;
        }

        $error = $this->storage->get('error');
        if ($this->storage->get('error')) {
            $error = $this->storage->get('error');
            $this->storage->set('error', null);
        }

        foreach ($cart->getErrors()->getElements() as $cartError) {
            if ($cartError instanceof \Netzkollektiv\EasyCredit\Cart\InterestError) {
                $this->storage->clear();
            }
        }

        $paymentMethodId = $this->paymentHelper->getPaymentMethodId($salesChannelContext->getContext());
        $isSelected = $paymentMethodId === $salesChannelContext->getPaymentMethod()->getId();

        try {
            $settings = $this->settings->getSettings($salesChannelContext->getSalesChannel()->getId());
            $checkout = $this->checkoutFactory->create($salesChannelContext);
        } catch (SettingsInvalidException $e) {
            $this->removePaymentMethodFromConfirmPage($event);

            return;
        }

        try {
            $agreement = $this->getCachedAgreement($checkout);
        } catch (\Exception $e) {
            $this->removePaymentMethodFromConfirmPage($event);

            return;
        }

        if ($isSelected) {
            if (is_null($error)) {
                try {
                    $checkout->isAvailable(
                        $this->quoteHelper->getQuote($salesChannelContext, $cart)
                    );
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        $event->getPage()->addExtension('easycredit', (new CheckoutData())->assign([
            'paymentMethodId' => $paymentMethodId,
            'isSelected' => $isSelected,
            'agreement' => $agreement,
            'paymentPlan' => $this->storage->get('payment_plan'),
            'error' => $error,
        ]));
    }

    protected function getCachedAgreement($checkout)
    {
        $agreement = '';
        $cacheItem = $this->cache->getItem('easycredit-agreement');
        if ($cacheItem->isHit() && $cacheItem->get()) {
            $agreement = $cacheItem->get();
        } else {
            $agreement = $checkout->getAgreement();

            $cacheItem->set($agreement);
            $this->cache->save($cacheItem);
        }

        return $agreement;
    }

    private function removePaymentMethodFromConfirmPage(CheckoutConfirmPageLoadedEvent $event): void
    {
        $paymentMethodCollection = $event->getPage()->getPaymentMethods();
        $paymentMethodCollection->remove($this->paymentHelper->getPaymentMethodId($event->getContext()));
    }
}
