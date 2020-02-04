<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Shopware\Core\Checkout\Order\OrderEntity;

class Order implements \Netzkollektiv\EasyCreditApi\Rest\QuoteInterface
{
    /**
     * @var OrderEntity
     */
    protected $order;

    public function __construct(
        OrderEntity $order
    ) {
        $this->order = $order;
    }

    public function getId(): ?string
    {
        return '';
    }

    public function getShippingMethod(): ?string
    {
        return '';
    }

    public function getGrandTotal(): float
    {
        return $this->order->getPrice()->getTotalPrice();
    }

    public function getBillingAddress(): ?Quote\Address
    {
        return null;
    }

    public function getShippingAddress(): Quote\ShippingAddress
    {
        $deliveries = $this->order->getDeliveries();
        if ($deliveries === null
            || $deliveries->first() === null
            || $deliveries->first()->getShippingOrderAddress() === null
        ) {
            throw new QuoteInvalidException('quote invalid');
        }

        return new Quote\ShippingAddress(
            $deliveries->first()->getShippingOrderAddress()
        );
    }

    public function getCustomer(): ?Quote\Customer
    {
        return null;
    }

    public function getItems(): array
    {
        $items = [];
        if ($this->order->getLineItems() !== null) {
            $items = $this->order->getLineItems()->getElements();
        }

        return $this->_getItems($items);
    }

    public function getSystem(): System
    {
        return new System();
    }

    /**
     * @param \Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity[] $items
     */
    protected function _getItems(array $items): array
    {
        $_items = [];
        foreach ($items as $item) {
            $quoteItem = new Quote\Item(
                $item
            );
            if ($quoteItem->getPrice() <= 0) {
                continue;
            }
            $_items[] = $quoteItem;
        }

        return $_items;
    }
}
