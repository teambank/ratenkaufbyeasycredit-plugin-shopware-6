<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Cart;

use Netzkollektiv\EasyCredit\Api\IntegrationFactory;
use Netzkollektiv\EasyCredit\Api\QuoteInvalidException;
use Netzkollektiv\EasyCredit\Api\Storage;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Monolog\Logger;

class Validator implements CartValidatorInterface
{
    protected $integrationFactory;

    protected $quoteHelper;

    protected $paymentHelper;

    protected $storage;

    public function __construct(
        IntegrationFactory $integrationFactory,
        QuoteHelper $quoteHelper,
        PaymentHelper $paymentHelper,
        Storage $storage,
        Logger $logger
    ) {
        $this->integrationFactory = $integrationFactory;
        $this->quoteHelper = $quoteHelper;
        $this->paymentHelper = $paymentHelper;
        $this->storage = $storage;
        $this->logger = $logger;
    }

    public function validate(
        Cart $cart,
        ErrorCollection $errors,
        SalesChannelContext $salesChannelContext
    ): void {

        try {
            $checkout = $this->integrationFactory->createCheckout(
                $salesChannelContext
            );
        } catch (\Throwable $e) {
            $this->storage->clear();

            return;
        }

        try {
            $quote = $this->quoteHelper->getQuote($salesChannelContext, $cart);
        } catch (QuoteInvalidException $e) {
            $this->storage->clear();

            return;
        }

        if (!$this->paymentHelper->isSelected($salesChannelContext)) {
            $this->storage->clear();

            return;
        }

        if (!$this->storage->get('interest_amount')) {
            $this->logger->debug('InterestError: interest amount not set'); 
            $errors->add(new InterestError());

            return;
        }
        if (!$checkout->isAmountValid($quote)) {
            $this->logger->debug('InterestError: amount not valid');
            $errors->add(new InterestError());

            return;
        }
        if (!$checkout->verifyAddress($quote)) {
            $this->logger->debug('InterestError: address changed');
            $errors->add(new InterestError());

            return;
        }
    }
}
