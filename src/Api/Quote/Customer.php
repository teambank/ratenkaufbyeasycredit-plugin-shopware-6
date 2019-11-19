<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Api\Quote;

//use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
//use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;

class Customer implements \Netzkollektiv\EasyCreditApi\Rest\CustomerInterface
{
    protected $customer = null;

    public function __construct(
        CustomerEntity $customer,
        CustomerAddressEntity $billingAddress
    ) {
        $this->customer = $customer;
        $this->billingAddress = $billingAddress;
    }

    public function getPrefix()
    {
        if ($this->billingAddress->getSalutation()) {
            return $this->billingAddress->getSalutation()->getDisplayName();
        }
        if ($this->customer->getSalutation()) {
            return $this->customer->getSalutation()->getDisplayName();
        }
    }

    public function getFirstname()
    {
        return $this->customer->getFirstName();
    }

    public function getLastname()
    {
        return $this->customer->getLastName();
    }

    public function getEmail()
    {
        return $this->customer->getEmail();
    }

    public function getDob()
    {
        if ($this->customer->getBirthday() instanceof \DateTimeImmutable) {
            return $this->customer->getBirthday()->format('Y-m-d');
        }
    }

    public function getCompany()
    {
        return $this->customer->getCompany();
    }

    public function getTelephone()
    {
        return '';
    }

    public function isLoggedIn()
    {
        return !$this->customer->getGuest();
    }

    public function getCreatedAt()
    {
        if ($this->customer->getCreatedAt() instanceof \DateTimeImmutable) {
            return $this->customer->getCreatedAt()->format('Y-m-d');
        }
    }

    public function getOrderCount()
    {
        return $this->customer->getOrderCount();
    }
}
