<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Payment\Handler;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Payment
{
    private $paymentMethodRepository;

    private $paymentHandlerRegistry;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository,
        PaymentHandlerRegistry $paymentHandlerRegistry
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentHandlerRegistry = $paymentHandlerRegistry;
    }

    public function isSelected(SalesChannelContext $context, $paymentMethod = null): bool
    {
        return $this->getPaymentHandler($context, $paymentMethod) instanceof Handler;
    }

    /**
     * @return SynchronousPaymentHandlerInterface|AsynchronousPaymentHandlerInterface
     */
    public function getPaymentHandler(SalesChannelContext $context, $paymentMethod = null)
    {
        if (is_null($paymentMethod)) {
            $paymentMethod = $context->getPaymentMethod();
        }

        if (is_string($paymentMethod)) {
            $paymentMethod = $this->paymentMethodRepository->search(new Criteria([
                $paymentMethod,
            ]), $context->getContext())->first();
        }

        return $this->paymentHandlerRegistry
            ->getHandler($paymentMethod->getHandlerIdentifier());
    }
}
