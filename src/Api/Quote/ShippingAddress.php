<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

        return (bool) \mb_stripos(\implode(' ', $street), 'packstation');
    }
}
