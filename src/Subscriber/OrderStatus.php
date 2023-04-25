<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Netzkollektiv\EasyCredit\Api\IntegrationFactory;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use  Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Teambank\RatenkaufByEasyCreditApiV3\Model\CaptureRequest;
use Teambank\RatenkaufByEasyCreditApiV3\Model\RefundRequest;
use Teambank\RatenkaufByEasyCreditApiV3\Model\TransactionResponse;

class OrderStatus implements EventSubscriberInterface
{
    private SettingsServiceInterface $settings;

    private IntegrationFactory $integrationFactory;

    private $logger;

    public function __construct(
        SettingsServiceInterface $settingsService,
        IntegrationFactory $integrationFactory,
        LoggerInterface $logger
    ) {
        $this->settings = $settingsService;
        $this->integrationFactory = $integrationFactory;
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
            $transaction = $this->integrationFactory
                ->createTransactionApi()
                ->apiMerchantV3TransactionTransactionIdGet($txId);

            if ($transaction->getStatus() !== TransactionResponse::STATUS_REPORT_CAPTURE) {
                return;
            }

            $this->integrationFactory
                ->createTransactionApi()
                ->apiMerchantV3TransactionTransactionIdCapturePost(
                    $txId,
                    new CaptureRequest(['trackingNumber' => ''])
                );
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
            $transaction = $this->integrationFactory
                ->createTransactionApi()
                ->apiMerchantV3TransactionTransactionIdGet($txId);

            $this->integrationFactory
                ->createTransactionApi()
                ->apiMerchantV3TransactionTransactionIdRefundPost(
                    $txId,
                    new RefundRequest(['value' => $transaction->getOrderDetails()->getCurrentOrderValue()])
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
