<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Api;

use Netzkollektiv\EasyCredit\Setting\SettingStruct;

class Config extends \Netzkollektiv\EasyCreditApi\Config
{
    protected $settings;

    public function __construct(
        SettingStruct $settings
    ) {
        $this->settings = $settings;
    }

    public function getWebshopId(): string
    {
        $webshopId = $this->settings->getWebshopId();

        if (empty($webshopId)) {
            throw new \Exception('webshopId not configured');
        }

        return $webshopId;
    }

    public function getWebshopToken(): string
    {
        $webshopId = $this->settings->getApiPassword();

        if (empty($webshopId)) {
            throw new \Exception('apiPassword not configured');
        }

        return $webshopId;
    }
}
