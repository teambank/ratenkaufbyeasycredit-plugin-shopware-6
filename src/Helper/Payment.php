<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Payment\Handler;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Payment
{
    private $paymentMethodRepository;

    private $paymentHandlerRegistry;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        PaymentHandlerRegistry $paymentHandlerRegistry
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentHandlerRegistry = $paymentHandlerRegistry;
    }

    public function isSelected(SalesChannelContext $context, $paymentMethod = null)
    {
        return $this->getPaymentHandler($context, $paymentMethod) instanceof Handler;
    }

    public function getPaymentHandler(SalesChannelContext $context, $paymentMethod = null)
    {
        if (is_null($paymentMethod)) {
            $paymentMethod = $context->getPaymentMethod();
        }

        if (is_string($paymentMethod)) {
            $paymentMethod = $this->paymentMethodRepository->search(new Criteria([
                $paymentMethod,
            ]), $context->getContext())->first();
        }

        return $this->paymentHandlerRegistry
            ->getHandler($paymentMethod->getHandlerIdentifier());
    }

    public function getPaymentMethodId(Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('handlerIdentifier', Handler::class));

        return $this->paymentMethodRepository->searchIds($criteria, $context)->firstId();
    }
}
