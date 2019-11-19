<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Setting\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidApiCredentialsException extends ShopwareHttpException
{
    public function __construct()
    {
        parent::__construct('Provided API credentials are invalid');
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'NETZKOLLEKTIV_EASYCREDIT__INVALID_API_CREDENTIALS';
    }
}
