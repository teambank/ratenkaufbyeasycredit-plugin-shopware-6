<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api\Quote;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Teambank\RatenkaufByEasyCreditApiV3\Integration\Util\PrefixConverter;

class CustomerBuilder
{
    protected $customer;

    protected $billingAddress;

    public function __construct(PrefixConverter $prefixConverter) {
        $this->prefixConverter = $prefixConverter;
    }

    public function getPrefix(): ?string
    {
        $prefix = null;
        if ($this->customer->getSalutation()) {
            $prefix = $this->customer->getSalutation()->getDisplayName();
        }
	    if ($this->billingAddress->getSalutation()) {
	        $prefix = $this->billingAddress->getSalutation()->getDisplayName();
        }

        return $this->prefixConverter->convert($prefix);
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

    public function build(
        CustomerEntity $customer,
        CustomerAddressEntity $billingAddress
    ) {
        $this->billingAddress = $billingAddress;
        $this->customer = $customer;

        return new \Teambank\RatenkaufByEasyCreditApiV3\Model\Customer([
            'gender' => $this->getPrefix(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'birthDate' => $this->getDob(),
            'contact' => new \Teambank\RatenkaufByEasyCreditApiV3\Model\Contact([
                'email' => $this->customer->getEmail()
            ]),
            'companyName' => $this->customer->getActiveBillingAddress() ? $this->customer->getActiveBillingAddress()->getCompany() : null
        ]);
    }
}
