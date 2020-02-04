<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Setting\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class SettingsInvalidException extends ShopwareHttpException
{
    public function __construct(string $missingSetting)
    {
        parent::__construct(
            'Required setting "{{ missingSetting }}" is missing or invalid',
            ['missingSetting' => $missingSetting]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'NETZKOLLEKTIV_EASYCREDIT__REQUIRED_SETTING_INVALID';
    }
}
