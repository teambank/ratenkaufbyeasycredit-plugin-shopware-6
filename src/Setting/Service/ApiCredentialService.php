<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Setting\Service;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Setting\Exception\InvalidApiCredentialsException;

class ApiCredentialService implements ApiCredentialServiceInterface
{
    private $checkoutFactory;

    public function __construct(
        CheckoutFactory $checkoutFactory
    ) {
        $this->checkoutFactory = $checkoutFactory;
    }

    /**
     * @throws InvalidApiCredentialsException
     */
    public function testApiCredentials(string $webshopId, string $apiPassword): bool
    {
        if (!$webshopId || !$apiPassword) {
            throw new InvalidApiCredentialsException();
        }

        $checkout = $this->checkoutFactory->create(null, false);

        if (!$checkout->verifyCredentials($webshopId, $apiPassword)) {
            throw new InvalidApiCredentialsException();
        }

        return true;
    }
}
