<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Netzkollektiv\EasyCredit\Helper\MetaDataProvider;

class System implements \Netzkollektiv\EasyCreditApi\SystemInterface
{
    public function __construct(
        MetaDataProvider $metaDataProvider
    ) {
        $this->metaDataProvider = $metaDataProvider;
    }

    public function getSystemVendor(): string
    {
        return 'Shopware';
    }

    public function getSystemVersion(): string
    {
        return $this->metaDataProvider->getShopwareVersion();
    }

    public function getModuleVersion(): ?string
    {
        $json = file_get_contents(dirname(__FILE__) . '/../../composer.json');
        if ($json !== false) {
            $json = json_decode($json);
            if (isset($json->version)) {
                return $json->version;
            }
        }
    }

    public function getIntegration(): string
    {
        return 'PAYMENT_PAGE';
    }
}
