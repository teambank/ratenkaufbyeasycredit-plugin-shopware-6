<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Api\Quote;

class ShippingAddress extends Address implements \Netzkollektiv\EasyCreditApi\Rest\ShippingAddressInterface
{
    public function getIsPackstation(): bool
    {
        $street = [
            $this->address->getStreet(),
            $this->address->getAdditionalAddressLine1(),
            $this->address->getAdditionalAddressLine2(),
        ];

        return (bool) stripos(implode(' ', $street), 'packstation');
    }
}
