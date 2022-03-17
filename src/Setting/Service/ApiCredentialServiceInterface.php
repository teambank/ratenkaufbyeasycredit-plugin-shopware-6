<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Setting\Service;

interface ApiCredentialServiceInterface
{
    public function testApiCredentials(string $webshopId, string $apiPassword, string $apiSignature = null): bool;
}
