<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Setting\Service;

use Symfony\Component\HttpFoundation\Response;
use Netzkollektiv\EasyCredit\Api\CheckoutFactory;

class ApiCredentialService implements ApiCredentialServiceInterface
{
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

        try {
            $checkout = $this->checkoutFactory->create();
            return $checkout->verifyCredentials($webshopId, $apiPassword);
        } catch (ClientException $ce) {
            if ($ce->getCode() === Response::HTTP_UNAUTHORIZED) {
                throw new InvalidApiCredentialsException();
            }

            throw $ce;
        }
    }
}