<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Setting\Service;

interface ApiCredentialServiceInterface
{
    public function testApiCredentials(string $webshopId, string $apiPassword): bool;
}