<?php declare(strict_types=1);

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