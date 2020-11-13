<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Api\MerchantFactory;

class OrderStatus implements EventSubscriberInterface
{
    /**
     * @var MerchantFactory
     */
    private $merchantFactory;

    public function __construct(
        MerchantFactory $merchantFactory,
        LoggerInterface $logger
    )
    {
        $this->merchantFactory = $merchantFactory;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_delivery.state.shipped' => 'onOrderShipped',
            'state_enter.order_delivery.state.returned' => 'onOrderReturned'
        ];
    }
    
    public function onOrderShipped(OrderStateMachineStateChangeEvent $event) {
        if (!$txId = $this->getTransactionId($event)) {
            return;
        }

        try {
            $client = $this->merchantFactory->create();
            $client->confirmShipment($txId);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function onOrderReturned(OrderStateMachineStateChangeEvent $event) {
        file_put_contents('/tmp/bla',__METHOD__.PHP_EOL,FILE_APPEND);
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
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }            
    }

    protected function getTransactionId(OrderStateMachineStateChangeEvent $event) {
        $tx = $event->getOrder()->getTransactions()->first();

        if (!$tx && !isset($tx->getCustomFields()['easycredit_transaction_id'])) {
            return false;
        }
        return $tx->getCustomFields()['easycredit_transaction_id'];
    }
}

