<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Netzkollektiv\EasyCredit\Helper\MetaDataProvider;
use Netzkollektiv\EasyCredit\Api\Storage;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsServiceInterface;
use Netzkollektiv\EasyCredit\Api\Quote\AddressBuilder;
use Netzkollektiv\EasyCredit\Api\Quote\ItemBuilder;
use Netzkollektiv\EasyCredit\Api\Quote\CustomerBuilder;

use Teambank\RatenkaufByEasyCreditApiV3\Integration;
use Teambank\RatenkaufByEasyCreditApiV3\Model\Transaction;
use Teambank\RatenkaufByEasyCreditApiV3\Model\ShippingAddress;
use Teambank\RatenkaufByEasyCreditApiV3\Model\InvoiceAddress;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class QuoteBuilder
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var CustomerEntity
     */
    protected $customer;

    protected $settings;

    /**
     * @var Storage
     */
    protected $storage;

    public function __construct(
        MetaDataProvider $metaDataProvider,
        SettingsServiceInterface $settingsService,
        Storage $storage,
        UrlGeneratorInterface $router,
        AddressBuilder $addressBuilder,
        ItemBuilder $itemBuilder,
        CustomerBuilder $customerBuilder,
        SystemBuilder $systemBuilder
    ) {
        $this->metaDataProvider = $metaDataProvider;
        $this->settings = $settingsService;
        $this->storage = $storage;
        $this->router = $router;
        $this->addressBuilder = $addressBuilder;
        $this->itemBuilder = $itemBuilder;
        $this->customerBuilder = $customerBuilder;
        $this->systemBuilder = $systemBuilder;
    }

    public function getId(): ?string
    {
        if ($this->cart instanceof Cart) {
            return $this->cart->getToken();
        }
    }

    public function getShippingMethod(): ?string
    {
        $delivery = $this->cart->getDeliveries()->first();
        if ($delivery === null) {
            return '';
        }
        $shippingMethod = $delivery->getShippingMethod()->getName();

        if ($this->getIsClickAndCollect()) {
            $shippingMethod = '[Selbstabholung] '.$shippingMethod;
        }
        return $shippingMethod;
    }

    public function getIsClickAndCollect(): Bool {
        $delivery = $this->cart->getDeliveries()->first();
        if ($delivery === null) {
            return false;
        }
        
        return $delivery->getShippingMethod()->getId() 
            === $this->settings->getSettings($this->context->getSalesChannel()->getId())->getClickAndCollectShippingMethod();
    }

    public function getDuration(): ?string {
        return $this->storage->get('duration');
    }

    public function getGrandTotal(): float
    {
        return $this->cart->getPrice()->getTotalPrice();
    }

    public function getInvoiceAddress(): ?InvoiceAddress
    {
        if (!$address = $this->customer->getActiveBillingAddress()) {
            throw new QuoteInvalidException();
        }

        return $this->addressBuilder
            ->setAddress(new InvoiceAddress())
            ->build($address);
    }

    public function getShippingAddress(): ShippingAddress
    {
        $address = $this->cart->getDeliveries()->getAddresses()->first();
        if ($address === null) {
            throw new QuoteInvalidException();
        }

        return $this->addressBuilder
            ->setAddress(new ShippingAddress())
            ->build($address);
    }

    public function getCustomer()
    {
        if ($this->customer->getActiveBillingAddress() === null) {
            throw new QuoteInvalidException();
        }

        return $this->customerBuilder->build(
            $this->customer,
            $this->customer->getActiveBillingAddress()
        );
    }

    public function getSystem() {
        return $this->systemBuilder->build();
    }

    public function getItems(): array
    {
        return $this->_getItems(
            $this->cart->getLineItems()->getElements()
        );
    }

    /**
     * @param \Shopware\Core\Checkout\Cart\LineItem\LineItem[] $items
     */
    protected function _getItems(array $items): array
    {
        $_items = [];
        foreach ($items as $item) {
            $quoteItem = $this->itemBuilder->build($item, $this->context);
            if ($quoteItem->getPrice() <= 0) {
                continue;
            }
            $_items[] = $quoteItem;
        }

        return $_items;
    }

    protected function getRedirectLinks() {
        if (!$this->storage->get('sec_token')) {
            $this->storage->set('sec_token', uniqid());
        }
        
        return new \Teambank\RatenkaufByEasyCreditApiV3\Model\RedirectLinks([
            'urlSuccess' => $this->router->generate('frontend.easycredit.return', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'urlCancellation' => $this->router->generate('frontend.easycredit.cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'urlDenial' => $this->router->generate('frontend.easycredit.reject', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'urlAuthorizationCallback' =>  $this->router->generate('frontend.easycredit.authorize', [
                'secToken' => $this->storage->get('sec_token')
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);
    }

    public function build($cart, SalesChannelContext $context): Transaction {
        $this->cart = $cart;
        $this->context = $context;
        $this->customer = $context->getCustomer();

        if ($cart instanceof Cart && $cart->getDeliveries()->getAddresses()->first() === null) {
            throw new QuoteInvalidException();
        }
        if (!$this->customer) {
            throw new QuoteInvalidException();
        }

        return new Transaction([
            'financingTerm' => $this->getDuration(),
            'orderDetails' => new \Teambank\RatenkaufByEasyCreditApiV3\Model\OrderDetails([
                'orderValue' => $this->getGrandTotal(),
                'orderId' => $this->getId(),
                'numberOfProductsInShoppingCart' => 1,
                'invoiceAddress' => $this->getInvoiceAddress(),
                'shippingAddress' => $this->getShippingAddress(),
                'shoppingCartInformation' => $this->getItems()
            ]),
            'shopsystem' => $this->getSystem(),
            'customer' => $this->getCustomer(),
            'customerRelationship' => new \Teambank\RatenkaufByEasyCreditApiV3\Model\CustomerRelationship([
                'customerSince' => ($this->customer->getCreatedAt() instanceof \DateTimeImmutable) ? $this->customer->getCreatedAt()->format('Y-m-d') : null,
                'orderDoneWithLogin' => !$this->customer->getGuest(),
                'numberOfOrders' => $this->customer->getOrderCount(),
                'logisticsServiceProvider' => $this->getShippingMethod()      
            ]),
            'redirectLinks' => $this->getRedirectLinks()
        ]);
    }
}