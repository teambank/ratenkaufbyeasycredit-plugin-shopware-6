<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Controller;

use Netzkollektiv\EasyCredit\Setting\Service\ApiCredentialServiceInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class SettingsController extends AbstractController
{
    /**
     * @var ApiCredentialServiceInterface
     */
    private $apiCredentialTestService;

    public function __construct(ApiCredentialServiceInterface $apiService)
    {
        $this->apiCredentialTestService = $apiService;
    }

    /**
     * @Route("/api/_action/easycredit/validate-api-credentials", name="api.action.easycredit.validate.api.credentials", methods={"GET"})
     */
    public function validateApiCredentials(Request $request): JsonResponse
    {
        $webshopId = $request->query->get('webshopId');
        $apiPassword = $request->query->get('apiPassword');

        $credentialsValid = $this->apiCredentialTestService->testApiCredentials($webshopId, $apiPassword);

        return new JsonResponse(['credentialsValid' => $credentialsValid]);
    }

    /**
     * @Route("/api/v{version}/_action/easycredit/validate-api-credentials", name="api.action.easycredit.validate.api.credentials.legacy", methods={"GET"})
     */
    public function validateApiCredentialsLegacy(Request $request): JsonResponse
    {
        return $this->validateApiCredentials($request);
    }
}
