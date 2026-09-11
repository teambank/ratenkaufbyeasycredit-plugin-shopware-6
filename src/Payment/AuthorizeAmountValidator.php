<?php

declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Payment;

use Netzkollektiv\EasyCredit\Cart\Processor;
use Shopware\Core\Checkout\Order\OrderEntity;
use Teambank\EasyCreditApiV3\Model\TransactionInformation;

class AuthorizeAmountValidator
{
    /**
     * Ensures Shopware order value (goods, excl. interest) matches EasyCredit transaction orderValue.
     *
     * @throws \RuntimeException when amounts differ or EasyCredit value is missing
     */
    public function validate(OrderEntity $order, TransactionInformation $transaction): void
    {
        $shopwareAmount = $this->getShopwareOrderValue($order);
        $easyCreditAmount = $this->getEasyCreditOrderValue($transaction);

        if ($easyCreditAmount === null) {
            throw new \RuntimeException('EasyCredit order value is missing on transaction.');
        }

        if (round($shopwareAmount, 2) !== round($easyCreditAmount, 2)) {
            throw new \RuntimeException(\sprintf(
                'Order amount mismatch before authorize: Shopware %.2f !== EasyCredit %.2f',
                $shopwareAmount,
                $easyCreditAmount
            ));
        }
    }

    public function getShopwareOrderValue(OrderEntity $order): float
    {
        $amount = (float) $order->getAmountTotal();

        $lineItems = $order->getLineItems();
        if ($lineItems === null) {
            return $amount;
        }

        foreach ($lineItems->filterByType(Processor::LINE_ITEM_TYPE) as $interestItem) {
            $amount -= (float) $interestItem->getTotalPrice();
        }

        return $amount;
    }

    public function getEasyCreditOrderValue(TransactionInformation $transaction): ?float
    {
        $tx = $transaction->getTransaction();
        if ($tx !== null) {
            $orderDetails = $tx->getOrderDetails();
            if ($orderDetails !== null && $orderDetails->getOrderValue() !== null) {
                return (float) $orderDetails->getOrderValue();
            }
        }

        $decision = $transaction->getDecision();
        if ($decision !== null && $decision->getOrderValue() !== null) {
            return (float) $decision->getOrderValue();
        }

        return null;
    }
}
