<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Netzkollektiv\EasyCredit\Api\MerchantFactory;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderStatus implements EventSubscriberInterface
{
    /**
     * @var MerchantFactory
     */
    private $merchantFactory;

    public function __construct(
        SettingsServiceInterface $settingsService,
        MerchantFactory $merchantFactory,
        LoggerInterface $logger
    ) {
        $this->settings = $settingsService;
        $this->merchantFactory = $merchantFactory;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_delivery.state.shipped' => 'onOrderShipped',
            'state_enter.order_delivery.state.returned' => 'onOrderReturned',
        ];
    }

    public function onOrderShipped(OrderStateMachineStateChangeEvent $event): void
    {
        $markShipped = $this->settings
            ->getSettings($event->getSalesChannelId(), false)
            ->getMarkShipped();

        if (!$markShipped) {
            return;
        }

        if (!$txId = $this->getTransactionId($event)) {
            return;
        }

        try {
            $client = $this->merchantFactory->create();
            $client->confirmShipment($txId);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function onOrderReturned(OrderStateMachineStateChangeEvent $event): void
    {
        $markRefunded = $this->settings
            ->getSettings($event->getSalesChannelId(), false)
            ->getMarkRefunded();

        if (!$markRefunded) {
            return;
        }

        if (!$txId = $this->getTransactionId($event)) {
            return;
        }

        try {
            $client = $this->merchantFactory->create();
            $client->cancelOrder(
                $txId,
                'WIDERRUF_VOLLSTAENDIG',
                new \DateTime()
            );
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    protected function getTransactionId(OrderStateMachineStateChangeEvent $event)
    {
        $tx = $event->getOrder()->getTransactions()->first();

        if (!$tx || !isset($tx->getCustomFields()['easycredit_transaction_id'])) {
            return false;
        }

        return $tx->getCustomFields()['easycredit_transaction_id'];
    }
}
