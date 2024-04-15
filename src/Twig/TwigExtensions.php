<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;

class TwigExtensions extends AbstractExtension
{
    private PaymentHelper $paymentHelper;

    public function __construct(
        PaymentHelper $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('easyCreditPaymentType', [$this, 'getPaymentType']),
        ];
    }

    public function getPaymentType(PaymentMethodEntity $payment, SalesChannelContext $salesChannelContext)
    {
        return str_replace('_PAYMENT','', $this->paymentHelper
            ->getHandlerByPaymentMethodId($payment->getId())
            ->getPaymentType());
    }
}
