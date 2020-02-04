<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Payment;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Netzkollektiv\EasyCredit\Util\Lifecycle\ActivateDeactivate;
use Netzkollektiv\EasyCredit\Helper\OrderDataProvider;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Handler implements SynchronousPaymentHandlerInterface
{
    private $transactionStateHandler;

    private $orderTransactionRepo;

    private $orderDataProvider;

    private $checkoutFactory;

    private $quoteHelper;

    private $storage;

    public function __construct(
        OrderTransactionStateHandler $transactionStateHandler,
        EntityRepositoryInterface $orderTransactionRepo,
        OrderDataProvider $orderDataProvider,
        CheckoutFactory $checkoutFactory,
        QuoteHelper $quoteHelper,
        Storage $storage
    ) {
        $this->transactionStateHandler = $transactionStateHandler;
        $this->orderTransactionRepo = $orderTransactionRepo;
        $this->orderDataProvider = $orderDataProvider;

        $this->checkoutFactory = $checkoutFactory;
        $this->quoteHelper = $quoteHelper;
        $this->storage = $storage;
    }

    public function pay(SyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void
    {
        $checkout = $this->checkoutFactory->create(
            $salesChannelContext
        );

        $order = $this->orderDataProvider->getOrder($transaction->getOrder(), $salesChannelContext);

        $quote = $this->quoteHelper->getQuote($salesChannelContext, $order);

        try {
            if (!$checkout->isAmountValid($quote)
             || !$checkout->verifyAddressNotChanged($quote)
             || !$checkout->isApproved()
            ) {
                throw new SyncPaymentProcessException(
                    $transaction->getOrderTransaction()->getId(),
                    'Transaction not valid for capture'
                );
            }

            $checkout->capture();

            $this->transactionStateHandler->pay(
                $transaction->getOrderTransaction()->getId(),
                $salesChannelContext->getContext()
            );

            $this->addEasyCreditTransactionId(
                $transaction,
                $salesChannelContext->getContext()
            );
	} catch (\Exception $e) {
		echo $e->getMessage().PHP_EOL;
		echo $e->getTraceAsString().PHP_EOL;
exit;
            throw new SyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                'Could not complete transaction: ' . $e->getMessage().$e->getTraceAsString()
            );
        }
    }

    protected function addEasyCreditTransactionId(
        SyncPaymentTransactionStruct $transaction,
        Context $context
    ): void {
        $data = [
            'id' => $transaction->getOrderTransaction()->getId(),
            'customFields' => [
                ActivateDeactivate::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_ID => $this->storage->get('transaction_id'),
            ],
        ];
        $this->orderTransactionRepo->update([$data], $context);
    }
}
