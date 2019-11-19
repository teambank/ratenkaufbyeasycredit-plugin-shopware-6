<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Netzkollektiv\EasyCredit\Setting\Service\ApiCredentialServiceInterface;
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
     * @Route("/api/v{version}/_action/easycredit/validate-api-credentials", name="api.action.easycredit.validate.api.credentials", methods={"GET"})
     */
    public function validateApiCredentials(Request $request): JsonResponse
    {
        $webshopId = $request->query->get('webshopId');
        $apiPassword = $request->query->get('apiPassword');

        $credentialsValid = $this->apiCredentialTestService->testApiCredentials($webshopId, $apiPassword);

        return new JsonResponse(['credentialsValid' => $credentialsValid]);
    }
}
