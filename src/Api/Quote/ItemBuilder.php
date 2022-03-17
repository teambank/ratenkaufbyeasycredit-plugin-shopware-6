<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api\Quote;

use Netzkollektiv\EasyCredit\Helper\MetaDataProvider;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Teambank\RatenkaufByEasyCreditApiV3\Integration;
use Teambank\RatenkaufByEasyCreditApiV3\Model\ShoppingCartInformationItem;

class ItemBuilder
{
    /**
     * @var LineItem
     */
    protected $item;

    public function __construct(
        MetaDataProvider $metaDataProvider
    ) {
        $this->metaDataProvider = $metaDataProvider;
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
        if (method_exists($this->item, 'hasPayloadValue')
            && $this->item->hasPayloadValue('manufacturerId')
        ) {
            $manufacturer = $this->metaDataProvider->getManufacturer(
                $this->item->getPayloadValue('manufacturerId'),
                $this->context
            );
            if (isset($manufacturer->getTranslated()['name'])) {
                return $manufacturer->getTranslated()['name'];
            }
        }

        return '';
    }

    public function getCategory(): string
    {
        $categoryNames = [];

        if (method_exists($this->item, 'hasPayloadValue')
            && $this->item->hasPayloadValue('categoryIds') && is_array($this->item->getPayloadValue('categoryIds'))
        ) {
            $categories = $this->metaDataProvider->getCategories(
                $this->item->getPayloadValue('categoryIds'),
                $this->context
            );
            foreach ($categories as $category) {
                if ($category->getTranslated()['name']) {
                    $categoryNames[] = $category->getTranslated()['name'];
                }
            }
        }

        return mb_substr(implode(', ', $categoryNames), 0, 255);
    }

    protected function getSkus(): array
    {
        if (!method_exists($this->item,'getPayloadValue')) {
            return [];
        }
        
        $skus = [];
        foreach (array_filter([
            'shopware-id' => $this->item->getPayloadValue('productNumber'),
        ]) as $type => $sku) {
            $skus[] = new \Teambank\RatenkaufByEasyCreditApiV3\Model\ArticleNumberItem([
                'numberType' => $type,
                'number' => $sku
            ]);           
        }
        return $skus;
    }

    public function build($item, SalesChannelContext $context) {
        $this->item = $item;
        $this->context = $context;

        return new ShoppingCartInformationItem([
            'productName' => $item->getLabel(),
            'quantity' => $item->getQuantity(),
            'price' => $this->getPrice(),
            'manufacturer' => $this->getManufacturer(),
            'productCategory' => $this->getCategory(),
            'articleNumber' => $this->getSkus()
        ]);      
    }
}
