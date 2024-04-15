<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Helper;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Netzkollektiv\EasyCredit\Payment\Handler\InstallmentPaymentHandler;
use Netzkollektiv\EasyCredit\Payment\Handler\BillPaymentHandler;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;

class Payment
{
    private $paymentMethodRepository;

    private EntityRepository $salesChannelRepository;

    private PaymentHandlerRegistry $paymentHandlerRegistry;

    private array $paymentMethodIdCache = [];

    public function __construct(
        EntityRepository $paymentMethodRepository,
        EntityRepository $salesChannelRepository,
        PaymentHandlerRegistry $paymentHandlerRegistry
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->paymentHandlerRegistry = $paymentHandlerRegistry;
    }

    public function isSelected(SalesChannelContext $salesChannelContext, $paymentMethodId = null): bool
    {
        if ($paymentMethodId === null) {
            $paymentMethodId = $salesChannelContext->getPaymentMethod()->getId();
        }

        return $this->getPaymentMethods($salesChannelContext->getContext())
            ->filterByProperty('id', $paymentMethodId)
            ->count() > 0;
    }
    
    public function getPaymentMethodByHandler ($handlerClass, Context $context) {
        return $this->getPaymentMethods($context)
            ->filterByProperty('handlerIdentifier', $handlerClass)->first();
    }

    public function getPaymentMethodById($paymentId, Context $context)
    {
        return $this->getPaymentMethods($context)
            ->filterByProperty('id', $paymentId)->first();
    }

    public function getPaymentMethodByPaymentType($paymentType, Context $context)
    {
        return $this->getPaymentMethods($context)
            ->filter(function (PaymentMethodEntity $struct) use ($paymentType) {
                return $this->getHandlerByPaymentMethodId($struct->get('id'))->getPaymentType() === $paymentType;
            })->first();
    }

    public function getHandlerByPaymentMethodId($paymentMethodId) {
        // prefer the newer getPaymentMethodHandler instead of getHandler (removed from v6.5)
        return \method_exists($this->paymentHandlerRegistry, 'getPaymentMethodHandler') ?
            $this->paymentHandlerRegistry->getPaymentMethodHandler($paymentMethodId) :
            $this->paymentHandlerRegistry->getHandler($paymentMethodId);
    }

    public function getPaymentMethods(Context $context): EntityCollection
    {
        $cacheId = \sha1(\json_encode($context));
        if (!isset($this->paymentMethodIdCache[$cacheId])) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsAnyFilter('handlerIdentifier', [
                InstallmentPaymentHandler::class,
                BillPaymentHandler::class
            ]));

            $this->paymentMethodIdCache[$cacheId] = $this->paymentMethodRepository->search($criteria, $context)->getEntities();
        }
        return $this->paymentMethodIdCache[$cacheId];
    }

    public function isEasyCreditInSalesChannel(SalesChannelContext $salesChannelContext): bool
    {
        $context = $salesChannelContext->getContext();
        $paymentMethods = $this->getPaymentMethods($context);
        if ($paymentMethods->count() === 0) {
            return false;
        }

        return $this->getSalesChannelPaymentMethods($salesChannelContext->getSalesChannel(), $context)
            ->filter(static function (PaymentMethodEntity $struct) use ($paymentMethods) {
                return \in_array($struct->get('id'), $paymentMethods->getIds());
            })->count() > 0;
    }

    private function getSalesChannelPaymentMethods(
        SalesChannelEntity $salesChannelEntity,
        Context $context
    ): ?PaymentMethodCollection {
        $criteria = new Criteria([$salesChannelEntity->getId()]);
        $criteria->addAssociation('paymentMethods');
        
        /** @var SalesChannelEntity|null $result */
        $result = $this->salesChannelRepository->search($criteria, $context)->get($salesChannelEntity->getId());

        if (!$result) {
            return null;
        }

        return $result->getPaymentMethods();
    }
}
