<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Payment;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionActions;
use Shopware\Core\System\StateMachine\StateMachineRegistry;
use Shopware\Core\System\StateMachine\Transition;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;

use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;

class StateHandler
{
    /**
     * @var StateMachineRegistry
     */
    private $stateMachineRegistry;

    public function __construct(
        StateMachineRegistry $stateMachineRegistry,
        SettingsServiceInterface $settingsService
    )
    {
        $this->stateMachineRegistry = $stateMachineRegistry;
        $this->settings = $settingsService;
    }

    protected function getSelectedTransition($entityName, $entity, $status, $context) {
        $availableTransitions = $this->stateMachineRegistry->getAvailableTransitions(
            $entityName,
            $entity->getId(),
            'stateId',
            $context
        );
        foreach ($availableTransitions as $transition) {
            if ($transition->getToStateId() === $status) {
                return $transition;
            }
        }
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws StateMachineNotFoundException
     * @throws IllegalTransitionException
     * @throws StateMachineInvalidEntityIdException
     * @throws StateMachineInvalidStateFieldException
     */
    public function handleTransactionState(OrderTransactionEntity $transaction, SalesChannelContext $salesChannelContext): void
    {
        $paymentStatus = $this->settings->getSettings($salesChannelContext->getSalesChannel()->getId(), false)->getPaymentStatus();

        if ($transition = $this->getSelectedTransition('order_transaction', $transaction, $paymentStatus, $salesChannelContext->getContext())) {
            $this->stateMachineRegistry->transition(
                new Transition(
                    OrderTransactionDefinition::ENTITY_NAME,
                    $transaction->getId(),
                    $transition->getActionName(),
                    'stateId'
                ),
                $salesChannelContext->getContext()
            );
        }
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws StateMachineNotFoundException
     * @throws IllegalTransitionException
     * @throws StateMachineInvalidEntityIdException
     * @throws StateMachineInvalidStateFieldException
     */
    public function handleOrderState(OrderEntity $order, SalesChannelContext $salesChannelContext): void
    {
        $orderStatus = $this->settings->getSettings($salesChannelContext->getSalesChannel()->getId(), false)->getOrderStatus();

        if ($transition = $this->getSelectedTransition('order', $order, $orderStatus, $salesChannelContext->getContext())) {
            $this->stateMachineRegistry->transition(
                new Transition(
                    OrderDefinition::ENTITY_NAME,
                    $order->getId(),
                    $transition->getActionName(),
                    'stateId'
                ),
                $salesChannelContext->getContext()
            );
        }
    }
}
