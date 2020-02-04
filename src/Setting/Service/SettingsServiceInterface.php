<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Setting\Service;

use Netzkollektiv\EasyCredit\Setting\SettingStruct;

interface SettingsServiceInterface
{
    public function getSettings(?string $salesChannelId = null, bool $validate = true): SettingStruct;

    public function updateSettings(array $settings, ?string $salesChannelId = null): void;
}
