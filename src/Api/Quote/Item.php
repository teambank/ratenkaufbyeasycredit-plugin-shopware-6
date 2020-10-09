<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api\Quote;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

use Netzkollektiv\EasyCredit\Helper\MetaDataProvider;

class Item implements \Netzkollektiv\EasyCreditApi\Rest\ItemInterface
{
    /**
     * @var LineItem
     */
    protected $item;

    public function __construct(
        LineItem $item,
        MetaDataProvider $metaDataProvider,
        SalesChannelContext $context
    ) {
        $this->item = $item;
        $this->metaDataProvider = $metaDataProvider;
        $this->context = $context;
    }

    public function getName(): ?string
    {
        return $this->item->getLabel();
    }

    public function getQty(): int
    {
        return $this->item->getQuantity();
    }

    public function getPrice(): float
    {
        if ($this->item->getPrice() === null) {
            return 0;
        }

        $taxAmount = $this->item->getPrice()->getCalculatedTaxes()->getAmount();

        return $this->item->getPrice()->getTotalPrice() - $taxAmount;
    }

    public function getManufacturer(): string
    {
        if ($manufacturerId = $this->item->getPayloadValue('manufacturerId')) {
            $manufacturer = $this->metaDataProvider->getManufacturer(
                $manufacturerId,
                $this->context
            );
            if ($manufacturer->getTranslated()['name']) {
                return $manufacturer->getTranslated()['name'];
            }
        }
        return '';
    }

    public function getCategory(): string
    {
        $categoryNames = [];

        $categoryIds = $this->item->getPayloadValue('categoryIds');
        if ($categoryIds && is_array($categoryIds)) {
            $categories = $this->metaDataProvider->getCategories(
                $categoryIds,
                $this->context
            );
            foreach ($categories as $category) {
                if ($category->getTranslated()['name']) {
                    $categoryNames[] = $category->getTranslated()['name'];
                }    
            }
        }
        return implode(', ',$categoryNames);
    }

    public function getSku(): array
    {
        return array_filter([
            'shopware-id' => $this->item->getPayloadValue('productNumber')
        ]);
    }
}
