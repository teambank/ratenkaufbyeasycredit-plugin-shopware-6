<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Cart;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Validator implements CartValidatorInterface
{
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
        if (count($this->storage->all()) === 0) {
            //return;
        }

        $checkout = $this->checkoutFactory->create(
            $salesChannelContext
        );
        $quote = $this->quoteHelper->getQuote($salesChannelContext, $cart);

        if (!$quote
            || !$this->paymentHelper->isSelected($salesChannelContext)
        ) {
            $this->storage->clear();

            return;
        }

        if (!$this->storage->get('interest_amount')) {
            $errors->add(new InterestError());

            return;
        }

        if (!$checkout->isAmountValid($quote)
            || !$checkout->verifyAddressNotChanged($quote)
        ) {
            $this->storage->clear();
            $errors->add(new InterestError());
        }
    }
}
