<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Flow\Action;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Event\OrderAware;
use Shopware\Core\Framework\Event\FlowEvent;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Netzkollektiv\EasyCredit\Service\TransactionService;

class CaptureAction extends FlowAction implements EventSubscriberInterface
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public static function getName(): string
    {
        return 'action.easycredit.capture';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            self::getName() => 'handle',
        ];
    }

    public function requirements(): array
    {
        return [OrderAware::class];
    }

    // SW >= v6.5.1.0
    public function handleFlow(StorableFlow $flow): void
    {
        if (!$flow->hasData(OrderAware::ORDER)) {
            return;
        }

        $order = $flow->getData(OrderAware::ORDER);
        $event = new OrderStateMachineStateChangeEvent('unknown', $order, $flow->getContext());
        $this->transactionService->captureTransaction($event);
    }

    // SW >= v6.4.6.0
    // SW <= v6.5.1.0
    public function handle(FlowEvent $event): void
    {
        if (!$event->getEvent() instanceof OrderAware) {
            return;
        }

        $this->transactionService->captureTransaction($event->getEvent());
    }
}
