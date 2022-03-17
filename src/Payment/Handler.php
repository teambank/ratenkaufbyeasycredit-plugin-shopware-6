<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Payment;

use Monolog\Logger;
use Netzkollektiv\EasyCredit\Api\IntegrationFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\OrderDataProvider;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Netzkollektiv\EasyCredit\Util\Lifecycle\ActivateDeactivate;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Netzkollektiv\EasyCredit\EasyCreditRatenkauf;

class Handler implements SynchronousPaymentHandlerInterface
{
    private $orderTransactionRepo;

    private $orderDataProvider;

    private $integrationFactory;

    private $quoteHelper;

    private $storage;

    private $logger;

    public function __construct(
        EntityRepositoryInterface $orderTransactionRepo,
        OrderDataProvider $orderDataProvider,
        IntegrationFactory $integrationFactory,
        QuoteHelper $quoteHelper,
        Storage $storage,
        Logger $logger
    ) {
        $this->orderTransactionRepo = $orderTransactionRepo;
        $this->orderDataProvider = $orderDataProvider;

        $this->integrationFactory = $integrationFactory;
        $this->quoteHelper = $quoteHelper;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function pay(SyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void
    {
        $checkout = $this->integrationFactory->createCheckout(
            $salesChannelContext
        );

        $order = $this->orderDataProvider->getOrder($transaction->getOrder(), $salesChannelContext);

        $quote = $this->quoteHelper->getQuote($salesChannelContext, $order);

        try {
            if (!$checkout->isAmountValid($quote)
                || !$checkout->verifyAddress($quote)
                || !$checkout->isApproved()
            ) {
                throw new SyncPaymentProcessException(
                    $transaction->getOrderTransaction()->getId(),
                    'Transaction not valid for capture'
                );
            }

            $checkout->authorize(null, $order->getOrderNumber());

            $this->addEasyCreditTransactionCustomFields(
                $transaction,
                $salesChannelContext->getContext()
            );
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            throw new SyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                'Could not complete transaction: ' . $e->getMessage() . $e->getTraceAsString()
            );
        }
    }

    protected function addEasyCreditTransactionCustomFields(
        SyncPaymentTransactionStruct $transaction,
        Context $context
    ): void {
        $data = [
            'id' => $transaction->getOrderTransaction()->getId(),
            'customFields' => [
                EasyCreditRatenkauf::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_ID => $this->storage->get('transaction_id'),
                EasyCreditRatenkauf::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_SEC_TOKEN => $this->storage->get('sec_token'),
            ],
        ];
        $this->orderTransactionRepo->update([$data], $context);
    }
}
