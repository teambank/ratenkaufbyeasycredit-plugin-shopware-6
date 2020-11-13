<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Controller;

use Netzkollektiv\EasyCredit\Api\MerchantFactory;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class TransactionsController extends AbstractController
{
    /**
     * @var MerchantFactory
     */
    private $merchantFactory;

    public function __construct(MerchantFactory $merchantFactory)
    {
        $this->merchantFactory = $merchantFactory;
    }

    /**
     * @Route("/api/v{version}/easycredit/transactions", name="api.easycredit.transactions", methods={"GET"})
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $client = $this->merchantFactory->create();

        $transactions = $client->searchTransactions();

        return new JsonResponse($transactions);
    }

    /**
     * @Route("/api/v{version}/easycredit/transaction", name="api.easycredit.transaction.post", methods={"GET"})
     */
    public function getTransaction(Request $request): JsonResponse
    {
        $transactionId = $request->query->get('id');

        $client = $this->merchantFactory->create();

        $transaction = $client->getTransaction($transactionId);

        return new JsonResponse($transaction);
    }

    /**
     * @Route("/api/v{version}/easycredit/transaction", name="api.easycredit.transaction.get", methods={"POST"})
     */
    public function updateTransaction(Request $request): JsonResponse
    {
        $client = $this->merchantFactory->create();

        $params = $request->request->all();
        switch ($params['status']) {
            case 'LIEFERUNG':
                $client->confirmShipment($params['id']);
                break;
            case 'WIDERRUF_VOLLSTAENDIG':
            case 'WIDERRUF_TEILWEISE':
            case 'RUECKGABE_GARANTIE_GEWAEHRLEISTUNG':
            case 'MINDERUNG_GARANTIE_GEWAEHRLEISTUNG':
                $client->cancelOrder(
                    $params['id'],
                    $params['status'],
                    \DateTime::createFromFormat('Y-d-m', $params['date']),
                    $params['amount']
                );
                break;
        }

        return new JsonResponse(true);
    }
}
