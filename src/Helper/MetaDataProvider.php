<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Helper;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class MetaDataProvider
{
    private $manufacturerRepository;

    private $categoryRepository;

    private $shopwareVersion;

    public function __construct(
        EntityRepository $manufacturerRepository,
        EntityRepository $categoryRepository,
        $shopwareVersion
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->categoryRepository = $categoryRepository;
        $this->shopwareVersion = $shopwareVersion;
    }

    public function getManufacturer(string $manufacturerId, SalesChannelContext $salesChannelContext)
    {
        $criteria = new Criteria([$manufacturerId]);

        return $this->manufacturerRepository->search($criteria, $salesChannelContext->getContext())->first();
    }

    public function getCategories(array $categoryIds, SalesChannelContext $salesChannelContext)
    {
        $criteria = new Criteria($categoryIds);

        return $this->categoryRepository->search($criteria, $salesChannelContext->getContext());
    }

    public function getShopwareVersion()
    {
        return $this->shopwareVersion;
    }
}
