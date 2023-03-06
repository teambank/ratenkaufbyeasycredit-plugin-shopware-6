<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Service;

use Shopware\Core\Checkout\Customer\SalesChannel\AbstractRegisterRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Newsletter\Exception\SalesChannelDomainNotFoundException;

use Teambank\RatenkaufByEasyCreditApiV3\Model\TransactionInformation as EasyCreditTransaction;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;


class CustomerService {

    public const EXPRESS_ACTIVE = 'easyCreditExpressActive';

    private AbstractRegisterRoute $registerRoute;

    private EntityRepositoryInterface $countryRepository;

    private EntityRepositoryInterface $salutationRepository;

    private SystemConfigService $systemConfigService;

    private PaymentHelper $paymentHelper;

    private AbstractSalesChannelContextFactory $salesChannelContextFactory;

    private CartService $cartService;

    public function __construct(
        AbstractRegisterRoute $registerRoute,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EntityRepositoryInterface $countryRepository,
        EntityRepositoryInterface $salutationRepository,
        SystemConfigService $systemConfigService,
        PaymentHelper $paymentHelper,
        CartService $cartService
    ) {
        $this->registerRoute = $registerRoute;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->countryRepository = $countryRepository;
        $this->salutationRepository = $salutationRepository;
        $this->systemConfigService = $systemConfigService;
        $this->paymentHelper = $paymentHelper;
        $this->cartService = $cartService;
    }

    private function getRegisterCustomerDataBag(EasyCreditTransaction $transaction, SalesChannelContext $salesChannelContext): RequestDataBag
    {
        $salutationId = $this->getSalutationId($salesChannelContext->getContext());

        $customer = $transaction->getTransaction()->getCustomer();
        $contact = $customer->getContact();
        $address = $transaction->getTransaction()->getOrderDetails()->getShippingAddress();

        $countryId = $this->getCountryId($address->getCountry(), $salesChannelContext->getContext());


        return new RequestDataBag([
            'guest' => true,
            'storefrontUrl' => $this->getStorefrontUrl($salesChannelContext),
            'salutationId' => $salutationId,
            'email' => $contact->getEmail(),
            'firstName' => $address->getFirstname(),
            'lastName' => $address->getLastname(),
            'billingAddress' => $this->getBillingAddress($transaction, $salesChannelContext->getContext(), $salutationId),
            'acceptedDataProtection' => true
        ]);
    }

    /**
     * @return array<string, string|null>
     */
    private function getBillingAddress(EasyCreditTransaction $transaction, Context $context, ?string $salutationId = null): array
    {
        $address = $transaction->getTransaction()->getOrderDetails()->getShippingAddress();
        $countryId = $this->getCountryId($address->getCountry(), $context);

        return [
            'firstName' => $address->getFirstname(),
            'lastName' => $address->getLastname(),
            'salutationId' => $salutationId,
            'street' => $address->getAddress(),
            'zipcode' => $address->getZip(),
            'countryId' => $countryId,
            'phoneNumber' => $transaction->getTransaction()->getCustomer()->getContact()->getMobilePhoneNumber(),
            'city' => $address->getCity()
        ];
    }

    public function handleExpress(EasyCreditTransaction $transaction, SalesChannelContext $salesChannelContext) {
        $newContextToken = $this->registerCustomer($transaction, $salesChannelContext);

        $newSalesChannelContext = $this->salesChannelContextFactory->create(
            $newContextToken,
            $salesChannelContext->getSalesChannel()->getId()
        );

        $cart = $this->cartService->getCart($newSalesChannelContext->getToken(), $salesChannelContext);
        $this->cartService->recalculate($cart, $newSalesChannelContext);
    }

    private function registerCustomer(EasyCreditTransaction $transaction, SalesChannelContext $salesChannelContext): string
    {
        $salesChannelContext->getContext()->addExtension(self::EXPRESS_ACTIVE, new ArrayStruct());
        $customerDataBag = $this->getRegisterCustomerDataBag($transaction, $salesChannelContext);
        $response = $this->registerRoute->register($customerDataBag, $salesChannelContext, false);
        $salesChannelContext->getContext()->removeExtension(self::EXPRESS_ACTIVE);

        $newToken = $response->headers->get(PlatformRequest::HEADER_CONTEXT_TOKEN);

        if ($newToken === null || $newToken === '') {
            throw new MissingRequestParameterException(PlatformRequest::HEADER_CONTEXT_TOKEN);
        }

        return $newToken;
    }


    private function getCountryId(string $code, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('iso', $code));

        return $this->countryRepository->searchIds($criteria, $context)->firstId();
    }

    private function getSalutationId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->setLimit(1);
        $criteria->addFilter(new EqualsFilter('salutationKey', 'not_specified'));

        $salutationId = $this->salutationRepository->searchIds($criteria, $context)->firstId();

        if ($salutationId !== null) {
            return $salutationId;
        }

        $salutationId = $this->salutationRepository->searchIds($criteria->resetFilters(), $context)->firstId();

        if ($salutationId === null) {
            throw new \RuntimeException('No salutation found in Shopware');
        }

        return $salutationId;
    }

    private function getStorefrontUrl(SalesChannelContext $salesChannelContext): string
    {
        $salesChannel = $salesChannelContext->getSalesChannel();
        $domainUrl = $this->systemConfigService->get('core.loginRegistration.doubleOptInDomain', $salesChannel->getId());

        if (\is_string($domainUrl) && $domainUrl !== '') {
            return $domainUrl;
        }

        $domains = $salesChannel->getDomains();
        if ($domains === null) {
            throw new SalesChannelDomainNotFoundException($salesChannel);
        }

        $domain = $domains->first();
        if ($domain === null) {
            throw new SalesChannelDomainNotFoundException($salesChannel);
        }

        return $domain->getUrl();
    }
}
