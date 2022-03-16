<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api\Quote;

use Netzkollektiv\EasyCredit\Api\QuoteInvalidException;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class Address implements \Netzkollektiv\EasyCreditApi\Rest\AddressInterface
{
    /**
     * @var CustomerAddressEntity|OrderAddressEntity
     */
    protected $address;

    public function __construct(
        Entity $address
    ) {
        if (!$address instanceof CustomerAddressEntity
            && !$address instanceof OrderAddressEntity) {
            throw new QuoteInvalidException();
        }

        $this->address = $address;
    }

    public function getFirstname(): string
    {
        return $this->address->getFirstName();
    }

    public function getLastname(): string
    {
        return $this->address->getLastName();
    }

    public function getStreet(): string
    {
        return \trim($this->address->getStreet());
    }

    public function getStreetAdditional(): string
    {
        return \trim(\implode(' ', [
            $this->address->getAdditionalAddressLine1(),
            $this->address->getAdditionalAddressLine2(),
        ]));
    }

    public function getPostcode(): string
    {
        return $this->address->getZipcode();
    }

    public function getCity(): string
    {
        return $this->address->getCity();
    }

    public function getCountryId(): ?string
    {
        if ($this->address->getCountry() !== null) {
            return $this->address->getCountry()->getIso();
        }
    }
}
