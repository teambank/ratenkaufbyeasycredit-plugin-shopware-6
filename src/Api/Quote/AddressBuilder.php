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

class AddressBuilder
{
    protected $address = null;

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function build($address) {
        if (!$address instanceof CustomerAddressEntity
            && !$address instanceof OrderAddressEntity) {
            throw new QuoteInvalidException();
        }

        $this->address['firstName'] = $address->getFirstName();
        $this->address['lastName'] = $address->getLastName();
        $this->address['address'] = $address->getStreet();
        $this->address['additionalAddressInformation'] = trim(implode(' ', [
            $address->getAdditionalAddressLine1(),
            $address->getAdditionalAddressLine2(),
        ]));
        $this->address['zip'] = $address->getZipCode();
        $this->address['city'] = $address->getCity();
        $this->address['country'] = $address->getCountry()->getIso();

        return $this->address;
    }
}
