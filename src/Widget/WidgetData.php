<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Widget;

use Shopware\Core\Framework\Struct\Struct;

class WidgetData extends Struct
{
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $widgetSelector;

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getWidgetSelector(): string
    {
        return $this->widgetSelector;
    }
}
