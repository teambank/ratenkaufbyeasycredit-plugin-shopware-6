<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Setting\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\Uuid\Exception\InvalidUuidException;
use Shopware\Core\System\SystemConfig\Exception\InvalidDomainException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;
use Netzkollektiv\EasyCredit\Setting\SettingStruct;
use Netzkollektiv\EasyCredit\Setting\SettingStructValidator;

class SettingsService implements SettingsServiceInterface
{
    public const SYSTEM_CONFIG_DOMAIN = 'NetzkollektivEasyCredit.settings.';

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @throws InvalidDomainException
     * @throws PayPalSettingsInvalidException
     * @throws InconsistentCriteriaIdsException
     * @throws InvalidUuidException
     */
    public function getSettings(?string $salesChannelId = null): SettingStruct
    {
        $values = $this->systemConfigService->getDomain(
            self::SYSTEM_CONFIG_DOMAIN,
            $salesChannelId,
            true
        );

        $propertyValuePairs = [];

        /** @var string $key */
        foreach ($values as $key => $value) {
            $property = (string) mb_substr($key, \mb_strlen(self::SYSTEM_CONFIG_DOMAIN));
            if ($property === '') {
                continue;
            }
            $propertyValuePairs[$property] = $value;
        }

        $settingsEntity = new SettingStruct();
        $settingsEntity->assign($propertyValuePairs);
        SettingStructValidator::validate($settingsEntity);

        return $settingsEntity;
    }

    public function updateSettings(array $settings, ?string $salesChannelId = null): void
    {
        foreach ($settings as $key => $value) {
            $this->systemConfigService->set(
                self::SYSTEM_CONFIG_DOMAIN . $key,
                $value,
                $salesChannelId
            );
        }
    }
}