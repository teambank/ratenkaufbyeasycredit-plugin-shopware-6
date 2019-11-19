<?php
namespace Netzkollektiv\EasyCredit\Api\Quote;

class ShippingAddress extends Address implements \Netzkollektiv\EasyCreditApi\Rest\ShippingAddressInterface {

    public function getIsPackstation() {
        $street = [
            $this->address->getStreet(),
            $this->address->getAdditionalAddressLine1(),
            $this->address->getAdditionalAddressLine2()
        ];
        return stripos(implode(' ',$street), 'packstation');
    }
}