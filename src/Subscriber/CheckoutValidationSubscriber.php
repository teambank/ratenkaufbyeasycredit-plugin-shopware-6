<?php

declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Netzkollektiv\EasyCredit\Payment\Handler as EasyCreditPaymentHandler;
use Netzkollektiv\EasyCredit\Api\IntegrationFactory;

use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;
use Shopware\Core\Framework\Validation\BuildValidationEvent;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;


class CheckoutValidationSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private PaymentHandlerRegistry $paymentHandlerRegistry;

    private IntegrationFactory $integrationFactory;

    public function __construct(
        RequestStack $requestStack,
        PaymentHandlerRegistry $paymentHandlerRegistry,
        IntegrationFactory $integrationFactory
    ) {
        $this->requestStack = $requestStack;
        $this->paymentHandlerRegistry = $paymentHandlerRegistry;
        $this->integrationFactory = $integrationFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'framework.validation.order.create' => 'validateTransaction',
        ];
    }

    public function validateTransaction(BuildValidationEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return;
        }

        $salesChannelContext = $this->getSalesChannelContextFromRequest($request);
        $paymentMethodId = $salesChannelContext->getPaymentMethod()->getId();

        // prefer the newer getPaymentMethodHandler instead of getHandler (removed from v6.5)
        $paymentHandler = \method_exists($this->paymentHandlerRegistry,'getPaymentMethodHandler') ? 
            $this->paymentHandlerRegistry->getPaymentMethodHandler($paymentMethodId) :
            $this->paymentHandlerRegistry->getHandler($paymentMethodId);

        $checkout = $this->integrationFactory->createCheckout(
            $salesChannelContext
        );

        if ($paymentHandler instanceof EasyCreditPaymentHandler) {
            if (!$checkout->isApproved()) {
                throw new ConstraintViolationException(new ConstraintViolationList([
                    new ConstraintViolation(
                        '',
                        '',
                        [],
                        null,
                        '/easycredit',
                        $salesChannelContext->getPaymentMethod()->getName(),
                        null,
                        'EASYCREDIT_TRANSATION_NOT_APRROVED'
                    )
                ]), $request->request->all());
            }
        }
    }

    private function getSalesChannelContextFromRequest(Request $request): SalesChannelContext
    {
        return $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
    }
}
