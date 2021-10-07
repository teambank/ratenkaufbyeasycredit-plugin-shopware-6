<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api\Quote;

//use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
//use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;

class Customer implements \Netzkollektiv\EasyCreditApi\Rest\CustomerInterface
{
    protected $customer;

    protected $billingAddress;

    public function __construct(
        CustomerEntity $customer,
        CustomerAddressEntity $billingAddress
    ) {
        $this->customer = $customer;
        $this->billingAddress = $billingAddress;
    }

    public function getPrefix(): ?string
    {
        if ($this->customer->getSalutation()) {
            return $this->customer->getSalutation()->getDisplayName();
        }
	if ($this->billingAddress->getSalutation()) {
	    return $this->billingAddress->getSalutation()->getDisplayName();
        }
    }

    public function getFirstname(): string
    {
        if ($this->customer->getGuest()) {
            return $this->billingAddress->getFirstName();
        }
        return $this->customer->getFirstName();
    }

    public function getLastname(): string
    {
        if ($this->customer->getGuest()) {
            return $this->billingAddress->getLastName();
        }
        return $this->customer->getLastName();
    }

    public function getEmail(): string
    {
        return $this->customer->getEmail();
    }

    public function getDob(): ?string
    {
        if ($this->customer->getBirthday() instanceof \DateTimeImmutable) {
            return $this->customer->getBirthday()->format('Y-m-d');
        }

        return null;
    }

    public function getCompany(): ?string
    {
        return $this->billingAddress->getCompany();
    }

    public function getTelephone(): string
    {
        return '';
    }

    public function isLoggedIn(): bool
    {
        return !$this->customer->getGuest();
    }

    public function getCreatedAt(): ?string
    {
        if ($this->customer->getCreatedAt() instanceof \DateTimeImmutable) {
            return $this->customer->getCreatedAt()->format('Y-m-d');
        }

        return null;
    }

    public function getOrderCount(): int
    {
        return $this->customer->getOrderCount();
    }
}
