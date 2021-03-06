<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Setting;

use Shopware\Core\Framework\Struct\Struct;

class SettingStruct extends Struct
{
    /**
     * @var string
     */
    protected $webshopId;

    /**
     * @var string
     */
    protected $apiPassword;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var bool
     */
    protected $removeInterest = true;

    /**
     * @var string|null
     */
    protected $clickAndCollectShippingMethod = null;

    /**
     * @var bool
     */
    protected $widgetEnabled = true;

    /**
     * @var string|null
     */
    protected $widgetSelectorProductDetail = '.product-detail-buy .product-detail-tax-container';

    /**
     * @var string|null
     */
    protected $widgetSelectorCart = '.checkout-aside-action';

    public function getWebshopId(): string
    {
        return $this->webshopId;
    }

    public function setWebshopId(string $webshopId): void
    {
        $this->webshopId = $webshopId;
    }

    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    public function setApiPassword(string $apiPassword): void
    {
        $this->apiPassword = $apiPassword;
    }

    public function getDebug(): bool
    {
        return (bool) $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function getRemoveInterest(): bool
    {
        return (bool) $this->removeInterest;
    }

    public function setRemoveInterest(bool $removeInterest): void
    {
        $this->removeInterest = $removeInterest;
    }

    public function getClickAndCollectShippingMethod(): ?string
    {
        return $this->clickAndCollectShippingMethod;
    }

    public function setClickAndCollectShippingMethod(string $shippingMethod): void
    {
        $this->clickAndCollectShippingMethod = $shippingMethod;
    }

    public function getWidgetEnabled(): bool
    {
        return (bool) $this->widgetEnabled;
    }

    public function setWidgetEnabled(bool $widgetEnabled): void
    {
        $this->widgetEnabled = $widgetEnabled;
    }

    public function setWidgetSelectorProductDetail(string $widgetSelectorProductDetail): void
    {
        $this->widgetSelectorProductDetail = $widgetSelectorProductDetail;
    }

    public function getWidgetSelectorProductDetail(): ?string
    {
        return $this->widgetSelectorProductDetail;
    }

    public function getWidgetSelectorCart(): ?string
    {
        return $this->widgetSelectorCart;
    }

    public function setWidgetSelectorCart(string $widgetSelectorCart): void
    {
        $this->widgetSelectorCart = $widgetSelectorCart;
    }
}
