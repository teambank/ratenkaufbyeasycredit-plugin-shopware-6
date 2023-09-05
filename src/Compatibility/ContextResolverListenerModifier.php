<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Compatibility;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Netzkollektiv\EasyCredit\Helper\MetaDataProvider;

/*
 *  Fix for SW < 6.4.11.0
 *  https://github.com/shopware/platform/blob/trunk/UPGRADE-6.4.md#removal-of-deprecated-route-specific-annotations
 */
class ContextResolverListenerModifier implements EventSubscriberInterface
{
    protected MetaDataProvider $metaDataProvider;

    public function __construct(
        MetaDataProvider $metaDataProvider
    ) {
        $this->metaDataProvider = $metaDataProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['fixContext', 0],
            ],
        ];
    }

    public function fixContext(ControllerEvent $event): void
    {
        if (\version_compare($this->metaDataProvider->getShopwareVersion(), '6.4.11.0', '>=')) {
            return;
        }

        $routeScope = $event->getRequest()->attributes->get('_routeScope');
        if (\is_array($routeScope) && \class_exists(RouteScope::class)) {
            $event->getRequest()->attributes->set('_routeScope', new RouteScope(['scopes' => $routeScope]));
        }
    }
}
