<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Compatibility;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/*
 *  Fix for SW < 6.5
 *  https://github.com/shopware/platform/blob/trunk/UPGRADE-6.5.md#entityrepositoryinterface-removal
 */
class EntityCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        try {
            $service = $container->findDefinition('Shopware\Core\Checkout\Payment\DataAbstractionLayer\PaymentMethodRepositoryDecorator');
        } catch (ServiceNotFoundException $e) {
            return;
        }
        $repositories = [
            'payment_method.repository',
            'sales_channel.repository'
        ];

        foreach ($repositories as $repositoryId) {
            $decorator = new Definition(
                EntityRepositoryForwardCompatibilityDecorator::class,
                [
                    new Reference($repositoryId.'.inner'),
                ]
            );
            $decorator->setDecoratedService(
                $repositoryId,
                $repositoryId . '.inner',
                \PHP_INT_MIN
            );
            $container->setDefinition($repositoryId . '.decorator', $decorator);
        }
    }
}
