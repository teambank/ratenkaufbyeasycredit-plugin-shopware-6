<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Cart;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Api\QuoteInvalidException;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Validator implements CartValidatorInterface
{
    protected $checkoutFactory;

    protected $quoteHelper;

    protected $paymentHelper;

    protected $storage;

    public function __construct(
        CheckoutFactory $checkoutFactory,
        QuoteHelper $quoteHelper,
        PaymentHelper $paymentHelper,
        Storage $storage
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->quoteHelper = $quoteHelper;
        $this->paymentHelper = $paymentHelper;
        $this->storage = $storage;
    }

    public function validate(
        Cart $cart,
        ErrorCollection $errors,
        SalesChannelContext $salesChannelContext
    ): void {
        try {
            $checkout = $this->checkoutFactory->create(
                $salesChannelContext
            );
        } catch (\Throwable $e) {
            $this->storage->clear();

            return;
        }

        try {
            $quote = $this->quoteHelper->getQuote($cart, $salesChannelContext);
        } catch (QuoteInvalidException $e) {
            $this->storage->clear();

            return;
        }

        if (!$this->paymentHelper->isSelected($salesChannelContext)) {
            $this->storage->clear();

            return;
        }

        if (!$this->storage->get('interest_amount')
            || !$checkout->isAmountValid($quote)
            || !$checkout->verifyAddressNotChanged($quote)
        ) {
            $errors->add(new InterestError());
        }
    }
}
