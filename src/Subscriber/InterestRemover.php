<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Doctrine\DBAL\Connection;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InterestRemover implements EventSubscriberInterface
{
    protected $recalculationService;

    private $orderLineItemRepository;

    public function __construct(
        SettingsServiceInterface $settingsService,
        Connection $connection,
        Storage $storage
    ) {
        $this->settings = $settingsService;
        $this->connection = $connection;
        $this->storage = $storage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'removeInterest',
        ];
    }

    public function removeInterest(CheckoutOrderPlacedEvent $event): void
    {
        $order = $event->getOrder();

        $interestLineItem = $order->getLineItems()
            ->filterByType(\Netzkollektiv\EasyCredit\Cart\Processor::LINE_ITEM_TYPE)
            ->first();

        $isEnabled = $this->settings
            ->getSettings($event->getSalesChannelId(), false)
            ->getRemoveInterest();

        if (!$isEnabled || !$interestLineItem) {
            return;
        }

        $this->connection->beginTransaction();

        try {
            $this->connection->executeQuery("
                UPDATE `order` o
                INNER JOIN order_line_item ol ON ol.order_id = o.id AND ol.type = 'easycredit-interest'
                Set 
                    o.price = JSON_REPLACE(o.price, 
                        '$.netPrice', ROUND(o.amount_net - ol.total_price, 2),
                        '$.totalPrice', ROUND(o.amount_total - ol.total_price, 2), 
                        '$.positionPrice', ROUND(o.position_price - ol.total_price, 2)
                    )
                WHERE o.id = ?;
            ", [
                Uuid::fromHexToBytes($order->getId()),
            ]);

            $this->connection->executeQuery("
                DELETE order_line_item FROM order_line_item
                INNER JOIN `order` o ON order_line_item.order_id = o.id
                WHERE 
                    o.id = ?
                    AND order_line_item.type = 'easycredit-interest';
            ", [
                Uuid::fromHexToBytes($order->getId()),
            ]);
            $this->storage->set('interest_amount', 0);
            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
        }
    }
}
