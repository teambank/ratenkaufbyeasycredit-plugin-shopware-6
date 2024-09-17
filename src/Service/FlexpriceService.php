<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Service;

use Shopware\Core\Framework\Rule\Collector\RuleConditionRegistry;
use Shopware\Core\Framework\Rule\Container\AndRule;
use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\CartService;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Framework\Uuid\Uuid;

class FlexpriceService {

    private EntityRepository $ruleRepository;

    private $cartService;

    public function __construct(
        EntityRepository $ruleRepository,
        $cartService
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->cartService = $cartService;
    }

    protected function getFlexpriceRule(Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new ContainsFilter('moduleTypes.types', 'easycredit-flexprice'));
        $rule = $this->ruleRepository->search($criteria, $context)->first();
        return $rule;
    }

    protected function evaluateRule($rule, Cart $cart, SalesChannelContext $salesChannelContext): bool
    {
        return $rule->getPayload()->match(new CartRuleScope($cart, $salesChannelContext));
    }

    public function getCartForProduct (SalesChannelContext $salesChannelContext, $product, $quantity = 1) {
        $cart = new Cart($salesChannelContext->getToken());

        $lineItem = (new LineItem(Uuid::randomHex(), LineItem::PRODUCT_LINE_ITEM_TYPE, $product->getId(), $quantity))
            ->setGood(true)
            ->setRemovable(true)
            ->setStackable(true);
        $cart = $this->cartService->add($cart, $lineItem, $salesChannelContext);
        return $cart;
    }

    public function shouldDisableFlexprice (SalesChannelContext $salesChannelContext, Cart $cart) {
        $evaluated = $this->evaluateRule(
            $this->getFlexpriceRule($salesChannelContext->getContext()),
            $cart,
            $salesChannelContext
        );
        return $evaluated;
    }
}
