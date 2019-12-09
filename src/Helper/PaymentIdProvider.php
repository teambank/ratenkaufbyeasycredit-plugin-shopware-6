<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Helper;

use Netzkollektiv\EasyCredit\Payment\Handler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class PaymentIdProvider
{
    private $paymentMethodRepository;

    public function __construct(
        EntityRepositoryInterface $paymentMethodRepository
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function getPaymentMethodId(Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('handlerIdentifier', Handler::class));

        return $this->paymentMethodRepository->searchIds($criteria, $context)->firstId();
    }
}
