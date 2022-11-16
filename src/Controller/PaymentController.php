<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Context;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannel\ContextSwitchRoute;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;

use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\EasyCreditRatenkauf;
use Netzkollektiv\EasyCredit\Payment\StateHandler;
use Netzkollektiv\EasyCredit\Api\IntegrationFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Webhook\OrderTransactionNotFoundException;
use Netzkollektiv\EasyCredit\Service\CustomerService;

use Teambank\RatenkaufByEasyCreditApiV3\Model\TransactionInformation;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class PaymentController extends StorefrontController
{
    private $integrationFactory;

    private $stateHandler;

    private $storage;

    private $customerService;

    private PaymentHelper $paymentHelper;

    private ContextSwitchRoute $contextSwitchRoute;

    private $orderTransactionRepository;

    public function __construct(
        IntegrationFactory $integrationFactory,
        StateHandler $stateHandler,
        EntityRepository $orderTransactionRepository,
        Storage $storage,
        PaymentHelper $paymentHelper,
        CustomerService $customerService,
        ContextSwitchRoute $contextSwitchRoute
    ) {
        $this->integrationFactory = $integrationFactory;
        $this->stateHandler = $stateHandler;
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->storage = $storage;
        $this->paymentHelper = $paymentHelper;
        $this->customerService = $customerService;
        $this->contextSwitchRoute = $contextSwitchRoute;
    }

    /**
     * @Route("/easycredit/cancel", name="frontend.easycredit.cancel", options={"seo"="false"}, methods={"GET"})
     */
    public function cancel(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/express", name="frontend.easycredit.express", options={"seo"="false"}, methods={"GET"})
     */
    public function express(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $this->storage
            ->set('express', true);

        $this->contextSwitchRoute->switchContext(new RequestDataBag([
            SalesChannelContextService::PAYMENT_METHOD_ID => $this->paymentHelper->getPaymentMethodId($salesChannelContext->getContext())
        ]), $salesChannelContext);

        $this->paymentHelper->startCheckout($salesChannelContext);

        if ($this->storage->get('error')) {
            return $this->redirectToRoute('frontend.checkout.cart.page');
        }
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/return", name="frontend.easycredit.return", options={"seo"="false"}, methods={"GET"})
     */
    public function return(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        try {
            $checkout = $this->integrationFactory->createCheckout($salesChannelContext);

            if (!$checkout->isInitialized()) {
                throw new \Exception(
                    'Payment was not initialized.'
                );
            }

            $transaction = $checkout->loadTransaction();

            if ($this->storage->get('express')) {
                $this->customerService->handleExpress($transaction, $salesChannelContext);

                $this->storage->set('express', false);
                $this->storage->set('express-ui', true);

                $cart = $this->cartService->getCart($salesChannelContext->getToken(), $salesChannelContext);
                $checkout->finalizeExpress($this->quoteHelper->getQuote($cart, $salesChannelContext));
            }

            return $this->redirectToRoute('frontend.checkout.confirm.page');
        } catch (\Throwable $e) {
            $this->storage->set('error', $e->getMessage());
            return $this->redirectToRoute('frontend.checkout.cart.page');
        }
    }

    /**
     * @Route("/easycredit/reject", name="frontend.easycredit.reject", options={"seo"="false"}, methods={"GET"})
     */
    public function reject(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/authorize/{secToken}/", name="frontend.easycredit.authorize", options={"seo"="false"}, methods={"GET"})
     */
    public function authorize(Request $request, SalesChannelContext $salesChannelContext): Response
    {
        $secToken = $request->attributes->get('secToken');
        $transactionId = $request->query->get('transactionId');

        try {
            if (!$transactionId) {
                throw new OrderTransactionNotFoundException([
                    'suffix' => 'no transaction ID provided'
                ]);
            }

            $orderTransaction = $this->getOrderTransaction(
                $transactionId,
                $secToken,
                $salesChannelContext->getContext()
            );

            $checkout = $this->integrationFactory->createCheckout($salesChannelContext);
            $tx = $checkout->loadTransaction($orderTransaction->getCustomFields()['easycredit_technical_transaction_id']);

            if ($tx->getStatus() !== TransactionInformation::STATUS_AUTHORIZED) {
                return new Response('payment status of transaction not updated as transaction status is not AUTHORIZED', Response::HTTP_CONFLICT);
            }

            $this->stateHandler->handleTransactionState(
                $orderTransaction,
                $salesChannelContext
            );
            $this->stateHandler->handleOrderState(
                $orderTransaction->getOrder(),
                $salesChannelContext
            );
            return new Response('payment status successfully set', Response::HTTP_OK);

        } catch (OrderTransactionNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getOrderTransaction ($transactionId, $secToken, Context $context) {
        $criteria = new Criteria();
        $criteria->addAssociation('order');
        $criteria->addFilter(
            new EqualsFilter(
                \sprintf('customFields.%s', EasyCreditRatenkauf::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_ID),
                $transactionId
            )
        );
        $criteria->addFilter(
            new EqualsFilter(
                \sprintf('customFields.%s', EasyCreditRatenkauf::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_SEC_TOKEN),
                $secToken
            )
        );

        /** @var OrderTransactionEntity|null $orderTransaction */
        $orderTransaction = $this->orderTransactionRepository->search($criteria, $context)->first();

        if ($orderTransaction === null) {
            throw new OrderTransactionNotFoundException([
                'suffix' => \sprintf('with order transaction_id ID "%s" (order transaction ID)', $transactionId)
            ]);
        }

        return $orderTransaction;
    }
}
