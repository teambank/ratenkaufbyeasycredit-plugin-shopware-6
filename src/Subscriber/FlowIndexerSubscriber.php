<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\Plugin\Event\PluginPostUpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Netzkollektiv\EasyCredit\EasyCreditRatenkauf;

class FlowIndexerSubscriber implements EventSubscriberInterface
{
    private EntityIndexerRegistry $indexerRegistry;

    public function __construct(EntityIndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    public static function getSubscribedEvents()
    {
        return [
            PluginPostUpdateEvent::class => 'runFlowIndexer',
        ];
    }

    public function runFlowIndexer ($event): void {
        if ($event->getPlugin() instanceof EasyCreditRatenkauf) {
            $this->indexerRegistry->sendIndexingMessage(['flow.indexer']);
        }
    }
}
