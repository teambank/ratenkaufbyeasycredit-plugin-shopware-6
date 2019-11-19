<?php
namespace Netzkollektiv\EasyCredit\Api\Quote;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;

class Address implements \Netzkollektiv\EasyCreditApi\Rest\AddressInterface {

    protected $address = array();

    public function __construct(
        Entity $address
    ) {
        if (!$address instanceof CustomerAddressEntity 
            && !$address instanceof OrderAddressEntity) {
                throw new QuoteInvalidException();
            }

        $this->address = $address;
    }

    public function getFirstname() {
        return $this->address->getFirstName();
    }

    public function getLastname() {
        return $this->address->getLastName();
    }

    public function getStreet() {
        return trim($this->address->getStreet());
    }

    public function getStreetAdditional() {
        return trim(implode(' ',array(
            $this->address->getAdditionalAddressLine1(),
            $this->address->getAdditionalAddressLine2()
        )));
    }

    public function getPostcode() {
        return $this->address->getZipcode();
    }
    public function getCity() {
        return $this->address->getCity();
    }
    public function getCountryId() {
        return $this->address->getCountry()->getIso();
    }
}