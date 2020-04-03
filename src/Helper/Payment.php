<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Payment\Handler;
use Netzkollektiv\EasyCredit\Payment\Handler as PaymentHandler;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class Payment
{
    private $paymentMethodRepository;

    private $paymentHandlerRegistry;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        EntityRepositoryInterface $salesChannelRepository,
        PaymentHandlerRegistry $paymentHandlerRegistry
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->paymentHandlerRegistry = $paymentHandlerRegistry;
    }

    public function isSelected(SalesChannelContext $context, $paymentMethod = null): bool
    {
        return $this->getPaymentHandler($context, $paymentMethod) instanceof Handler;
    }

    /**
     * @return SynchronousPaymentHandlerInterface|AsynchronousPaymentHandlerInterface
     */
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
        $criteria->addFilter(new EqualsFilter('handlerIdentifier', PaymentHandler::class));

        return $this->paymentMethodRepository->searchIds($criteria, $context)->firstId();
    }

    public function isPaymentMethodInSalesChannel(SalesChannelContext $salesChannelContext): bool
    {
        $context = $salesChannelContext->getContext();
        $paymentMethodId = $this->getPaymentMethodId($context);
        if (!$paymentMethodId) {
            return false;
        }

        $paymentMethods = $this->getSalesChannelPaymentMethods($salesChannelContext->getSalesChannel(), $context);
        if (!$paymentMethods) {
            return false;
        }

        if ($paymentMethods->get($paymentMethodId) instanceof PaymentMethodEntity) {
            return true;
        }

        return false;
    }

    private function getSalesChannelPaymentMethods(
        SalesChannelEntity $salesChannelEntity,
        Context $context
    ): ?PaymentMethodCollection {
        $salesChannelId = $salesChannelEntity->getId();
        $criteria = new Criteria([$salesChannelId]);
        $criteria->addAssociation('paymentMethods');
        /** @var SalesChannelEntity|null $result */
        $result = $this->salesChannelRepository->search($criteria, $context)->get($salesChannelId);

        if (!$result) {
            return null;
        }

        return $result->getPaymentMethods();
    }
}
