<?php

declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Test\Unit\Payment;

use Netzkollektiv\EasyCredit\Cart\Processor;
use Netzkollektiv\EasyCredit\Payment\AuthorizeAmountValidator;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Teambank\EasyCreditApiV3\Model\OrderDetails;
use Teambank\EasyCreditApiV3\Model\Transaction;
use Teambank\EasyCreditApiV3\Model\TransactionInformation;
use Teambank\EasyCreditApiV3\Model\TransactionSummary;

class AuthorizeAmountValidatorTest extends TestCase
{
    private AuthorizeAmountValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AuthorizeAmountValidator();
    }

    public function testValidateAcceptsMatchingAmounts(): void
    {
        $order = $this->createOrder(220.0);
        $tx = $this->createTransactionInformation(220.0);

        $this->validator->validate($order, $tx);
        $this->addToAssertionCount(1);
    }

    public function testValidateRejectsMismatch(): void
    {
        $order = $this->createOrder(220.0);
        $tx = $this->createTransactionInformation(200.0);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Order amount mismatch before authorize');

        $this->validator->validate($order, $tx);
    }

    public function testShopwareAmountExcludesInterestLineItem(): void
    {
        $order = $this->createOrder(234.6, 14.6);

        self::assertSame(220.0, $this->validator->getShopwareOrderValue($order));
    }

    public function testEasyCreditAmountFallsBackToDecision(): void
    {
        $tx = new TransactionInformation([
            'decision' => new TransactionSummary(['orderValue' => 200.0]),
        ]);

        self::assertSame(200.0, $this->validator->getEasyCreditOrderValue($tx));
    }

    private function createOrder(float $amountTotal, ?float $interest = null): OrderEntity
    {
        $order = new OrderEntity();
        $order->setUniqueIdentifier('order');
        $order->setAmountTotal($amountTotal);

        $lineItems = new OrderLineItemCollection();
        if ($interest !== null) {
            $interestItem = new OrderLineItemEntity();
            $interestItem->setUniqueIdentifier('interest');
            $interestItem->setId('interest');
            $interestItem->setType(Processor::LINE_ITEM_TYPE);
            $interestItem->setQuantity(1);
            $interestItem->setLabel('Interest');
            $interestItem->setPrice(new CalculatedPrice(
                $interest,
                $interest,
                new CalculatedTaxCollection(),
                new TaxRuleCollection()
            ));
            $interestItem->setTotalPrice($interest);
            $lineItems->add($interestItem);
        }
        $order->setLineItems($lineItems);

        return $order;
    }

    private function createTransactionInformation(float $orderValue): TransactionInformation
    {
        return new TransactionInformation([
            'transaction' => new Transaction([
                'orderDetails' => new OrderDetails([
                    'orderValue' => $orderValue,
                ]),
            ]),
            'decision' => new TransactionSummary([
                'orderValue' => $orderValue,
            ]),
        ]);
    }
}
