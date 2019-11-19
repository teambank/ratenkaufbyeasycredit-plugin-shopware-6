<?php declare(strict_types=1);

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