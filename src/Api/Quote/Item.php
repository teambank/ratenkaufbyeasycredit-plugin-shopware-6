<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api\Quote;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;

class Item implements \Netzkollektiv\EasyCreditApi\Rest\ItemInterface
{
    /**
     * @var LineItem
     */
    protected $item;

    public function __construct(
        LineItem $item
    ) {
        $this->item = $item;
        //$this->loadCategory();
    }

    /*private function getCategoryId() {
        $query = 'SELECT categoryID from s_articles_categories WHERE articleID = ?';

        $categoryId = $this->db->fetchOne(
            $query,
            array($this->articleId)
        );

        return $categoryId;
    }

    private function getCategoryDescriptions() {
        $query = 'SELECT description FROM s_categories WHERE id = ?';

        $categoryDescription = $this->db->fetchOne(
            $query,
            array($this->categoryId)
        );

        return $categoryDescription;
    }

    private function loadCategory() {
        $this->categoryId = $this->getCategoryId();

        if (!$this->categoryId) {
            $this->categoryName = '';
            return;
        }

        $this->categoryName = $this->getCategoryDescriptions();
    }*/

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
        return ''; //$this->manufacturer;
    }

    public function getCategory(): string
    {
        return ''; //$this->categoryName;
    }

    public function getSku(): array
    {
        return array_filter([
            'shopware-id' => isset($this->item->getPayload()['productNumber']) ? $this->item->getPayload()['productNumber'] : null,
            //'shopware-bestell-nr'   => $this->rawItem['ordernumber'],
            //'ean'                   => $this->rawItem['ean'],
            //'suppliernumber'        => $this->rawItem['suppliernumber']
        ]);
    }
}
