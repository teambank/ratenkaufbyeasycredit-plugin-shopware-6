<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Cart;

use Netzkollektiv\EasyCredit\Api\Storage;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\Exception\InvalidPayloadException;
use Shopware\Core\Checkout\Cart\Exception\InvalidQuantityException;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\Translation\TranslatorInterface;

class Collector implements CartDataCollectorInterface
{
    protected $storage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        Storage $storage,
        TranslatorInterface $translator
    ) {
        $this->storage = $storage;
        $this->translator = $translator;
    }

    /**
     * @throws InvalidPayloadException
     * @throws InvalidQuantityException
     */
    public function collect(CartDataCollection $data, Cart $cart, SalesChannelContext $context, CartBehavior $behavior): void
    {
        if (!$price = $this->getInterestPrice()) {
            return;
        }

        $data->set(Processor::DATA_KEY, new LineItemCollection([
            $this->buildInterestLineItem($price),
        ]));
    }

    public function getInterestPrice(): ?CalculatedPrice
    {
        if (!$this->storage->get('interest_amount')) {
            return null;
        }

        return new CalculatedPrice(
            (float) $this->storage->get('interest_amount'),
            (float) $this->storage->get('interest_amount'),
            new CalculatedTaxCollection(),
            new TaxRuleCollection()
        );
    }

    protected function buildInterestLineItem(CalculatedPrice $price): LineItem
    {
        $id = 'easycredit-interest';

        $interestItem = new LineItem($id, Processor::LINE_ITEM_TYPE);
        $interestItem->setLabel($this->translator->trans('checkout.interest-line-item'));
        $interestItem->setDescription($this->translator->trans('checkout.interest-line-item'));
        $interestItem->setGood(false);
        $interestItem->setRemovable(false);
        $interestItem->setPayloadValue('productNumber', '');

        $interestItem->setPrice($price);
        $interestItem->setReferencedId($id);

        // add custom content to our payload.
        // we need this as meta data information.
        /*$interestItem->setPayload(
            $this->buildPayload(
                $code,
                $discount,
                $promotion,
                $currencyId
            )
        );*/

        // add our lazy-validation rules.
        // this is required within the recalculation process.
        // if the requirements are not met, the calculation process
        // will remove our discount line item.
        //$interestItem->setRequirement($promotion->getPreconditionRule());

        return $interestItem;
    }
}
