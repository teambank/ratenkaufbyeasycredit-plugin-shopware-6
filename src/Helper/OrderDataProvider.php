<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Payment\Handler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class OrderDataProvider
{
    private $orderRepository;

    public function __construct(
        EntityRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return OrderEntity|null $order
     */
    public function getOrder(OrderEntity $order, SalesChannelContext $salesChannelContext)
    {
        $criteria = new Criteria([$order->getId()]);
        $criteria->addAssociation('transactions.stateMachineState');
        $criteria->addAssociation('transactions.paymentMethod');
        $criteria->addAssociation('lineItems');
        $criteria->addAssociation('deliveries.shippingMethod');
        $criteria->addAssociation('deliveries.shippingOrderAddress.country');
        $criteria->addAssociation('addresses.country');
        $criteria->addAssociation('addresses.salutation');
        
        return $this->orderRepository->search($criteria, $salesChannelContext->getContext())->first();
    }
}