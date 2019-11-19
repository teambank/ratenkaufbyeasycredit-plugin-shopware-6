<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Api;

use Shopware\Core\Checkout\Cart\Cart;

class Quote implements \Netzkollektiv\EasyCreditApi\Rest\QuoteInterface
{
    public function __construct(
        object $cart,
        $context
    ) {
        $this->cart = $cart;
        $this->context = $context;

        if ($this->cart instanceof Cart
            && !$this->cart->getDeliveries()->getAddresses()->first()
        ) {
            throw new QuoteInvalidException();
        }
    }

    public function getId()
    {
        return $this->cart->getToken();
    }

    public function getShippingMethod()
    {
        return $this->cart->getDeliveries()->first()->getShippingMethod()->getName();
    }

    public function getGrandTotal()
    {
        return $this->cart->getPrice()->getTotalPrice();
    }

    public function getBillingAddress()
    {
        if ($this->cart instanceof Cart) {
            return new Quote\Address(
                $this->context->getCustomer()->getActiveBillingAddress()
            );
        }
    }

    public function getShippingAddress()
    {
        $address = $this->cart->getDeliveries()->first();
        if ($this->cart instanceof Cart) {
            $address = $this->cart->getDeliveries()->getAddresses()->first();
        } else {
            $address = $this->cart->getDeliveries()->first()->getShippingOrderAddress();
        }

        return new Quote\ShippingAddress($address);
    }

    public function getCustomer()
    {
        if ($this->cart instanceof Cart) {
            return new Quote\Customer(
                $this->context->getCustomer(),
                $this->context->getCustomer()->getActiveBillingAddress()
            );
        }
    }

    public function getItems()
    {
        return $this->_getItems(
            $this->cart->getLineItems()->getElements()
        );
    }

    public function getSystem()
    {
        return new System();
    }

    protected function _getItems($items)
    {
        $_items = [];
        foreach ($items as $item) {
            $quoteItem = new Quote\Item(
                $item
            );
            if ($quoteItem->getPrice() === 0) {
                continue;
            }
            $_items[] = $quoteItem;
        }

        return $_items;
    }
}
