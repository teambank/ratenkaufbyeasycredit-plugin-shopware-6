<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

use Netzkollektiv\EasyCredit\Helper\MetaDataProvider;

class Quote implements \Netzkollektiv\EasyCreditApi\Rest\QuoteInterface
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var CustomerEntity
     */
    protected $customer;

    public function __construct(
        Cart $cart,
        MetaDataProvider $metaDataProvider,
        SalesChannelContext $context
    ) {
        if ($cart->getDeliveries()->getAddresses()->first() === null) {
            throw new QuoteInvalidException();
        }
        $customer = $context->getCustomer();
        if ($customer === null) {
            throw new QuoteInvalidException();
        }

        $this->cart = $cart;
        $this->context = $context;
        $this->customer = $customer;
        $this->metaDataProvider = $metaDataProvider;
    }

    public function getId(): ?string
    {
        if ($this->cart instanceof Cart) {
            return $this->cart->getToken();
        }
    }

    public function getShippingMethod(): ?string
    {
        $delivery = $this->cart->getDeliveries()->first();
        if ($delivery === null) {
            return '';
        }

        return $delivery->getShippingMethod()->getName();
    }

    public function getGrandTotal(): float
    {
        return $this->cart->getPrice()->getTotalPrice();
    }

    public function getBillingAddress(): Quote\Address
    {
        if ($this->customer->getActiveBillingAddress() === null) {
            throw new QuoteInvalidException();
        }

        return new Quote\Address(
            $this->customer->getActiveBillingAddress()
        );
    }

    public function getShippingAddress(): Quote\ShippingAddress
    {
        $address = $this->cart->getDeliveries()->getAddresses()->first();
        if ($address === null) {
            throw new QuoteInvalidException();
        }

        return new Quote\ShippingAddress($address);
    }

    public function getCustomer(): Quote\Customer
    {
        if ($this->customer->getActiveBillingAddress() === null) {
            throw new QuoteInvalidException();
        }

        return new Quote\Customer(
            $this->customer,
            $this->customer->getActiveBillingAddress()
        );
    }

    public function getItems(): array
    {
        return $this->_getItems(
            $this->cart->getLineItems()->getElements()
        );
    }

    public function getSystem(): System
    {
        return new System(
            $this->metaDataProvider
        );
    }

    /**
     * @param \Shopware\Core\Checkout\Cart\LineItem\LineItem[] $items
     */
    protected function _getItems(array $items): array
    {
        $_items = [];
        foreach ($items as $item) {
            $quoteItem = new Quote\Item(
                $item,
                $this->metaDataProvider,
                $this->context
            );
            if ($quoteItem->getPrice() <= 0) {
                continue;
            }
            $_items[] = $quoteItem;
        }

        return $_items;
    }
}
