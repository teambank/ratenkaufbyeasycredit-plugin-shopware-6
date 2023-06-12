<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Setting\Service;

use Netzkollektiv\EasyCredit\Setting\SettingStruct;
use Netzkollektiv\EasyCredit\Setting\SettingStructValidator;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SettingsService implements SettingsServiceInterface
{
    public const SYSTEM_CONFIG_DOMAIN = 'EasyCreditRatenkauf.config.';

    private $systemConfigService;

    private array $settingsCache;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getSettings(?string $salesChannelId = null, bool $validate = true): SettingStruct
    {
        if (!isset($this->settingsCache[$salesChannelId])) {
            $values = $this->systemConfigService->getDomain(
                self::SYSTEM_CONFIG_DOMAIN,
                $salesChannelId,
                true
            );

            $propertyValuePairs = [];

            foreach ($values as $key => $value) {
                $property = (string) \mb_substr($key, \mb_strlen(self::SYSTEM_CONFIG_DOMAIN));
                if ($property === '') {
                    continue;
                }
                $propertyValuePairs[$property] = $value;
            }

            $settingsEntity = new SettingStruct();
            $settingsEntity->assign($propertyValuePairs);

            $this->settingsCache[$salesChannelId] = $settingsEntity;
        }

        $settingsEntity = $this->settingsCache[$salesChannelId];

        if ($validate) {
            SettingStructValidator::validate($settingsEntity);
        }

        return $settingsEntity;
    }

    public function updateSettings(array $settings, ?string $salesChannelId = null): void
    {
        unset($this->settingsCache[$salesChannelId]);

        foreach ($settings as $key => $value) {
            $this->systemConfigService->set(
                self::SYSTEM_CONFIG_DOMAIN . $key,
                $value,
                $salesChannelId
            );
        }
    }
}
