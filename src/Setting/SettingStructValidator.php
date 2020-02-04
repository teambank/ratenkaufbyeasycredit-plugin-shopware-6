<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Setting;

use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;

class SettingStructValidator
{
    public static function validate(SettingStruct $settingsStruct): void
    {
        try {
            $settingsStruct->getWebshopId();
        } catch (\TypeError $error) {
            throw new SettingsInvalidException('webshopId');
        }

        try {
            $settingsStruct->getApiPassword();
        } catch (\TypeError $error) {
            throw new SettingsInvalidException('apiPassword');
        }
    }
}
