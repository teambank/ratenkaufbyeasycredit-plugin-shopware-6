<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit;

use Doctrine\DBAL\Connection;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Payment\Handler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\CustomField\CustomFieldTypes;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class NetzkollektivEasyCredit extends Plugin
{
    public const ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_ID = 'easycredit_transaction_id';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('easycredit_payment.xml');
        $loader->load('setting.xml');
    }

    public function getViewPaths(): array
    {
        $viewPaths = parent::getViewPaths();
        $viewPaths[] = 'Resources/views/storefront';

        return $viewPaths;
    }

    public function install(InstallContext $context): void
    {
        $this->addPaymentMethod($context->getContext());
    }

    public function uninstall(UninstallContext $context): void
    {
        $this->setPaymentMethodIsActive(false, $context->getContext());
        if ($context->keepUserData()) {
            parent::uninstall($context);

            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        $connection->exec('
DROP TABLE IF EXISTS netzkollektiv_ratenkaufbyeasycedit_setting_general;
');
        parent::uninstall($context);
    }

    public function activate(ActivateContext $context): void
    {
        $shopwareContext = $context->getContext();
        $this->setPaymentMethodIsActive(true, $shopwareContext);
        $this->activateOrderTransactionCustomField($shopwareContext);

        parent::activate($context);
    }

    public function deactivate(DeactivateContext $context): void
    {
        $shopwareContext = $context->getContext();
        $this->setPaymentMethodIsActive(false, $shopwareContext);
        $this->deactivateOrderTransactionCustomField($shopwareContext);

        parent::deactivate($context);
    }

    private function addPaymentMethod(Context $context): void
    {
        /** @var PluginIdProvider $pluginIdProvider */
        $pluginIdProvider = $this->container->get(PluginIdProvider::class);
        $pluginId = $pluginIdProvider->getPluginIdByBaseClass($this->getClassName(), $context);
        /** @var EntityRepositoryInterface $paymentRepository */
        $paymentRepository = $this->container->get('payment_method.repository');
        $paymentMethodId = (new PaymentHelper($paymentRepository))->getPaymentMethodId($context);

        if ($paymentMethodId !== null) {
            return;
        }

        $easycredit = [
            'handlerIdentifier' => Handler::class,
            'name' => 'ratenkauf by easyCredit',
            'description' => 'Einfach. Fair. Sicher - jetzt die einfachste Teilzahlungslösung Deutschlands mit Shopware 6 nutzen.',
            'pluginId' => $pluginId,
        ];

        $paymentRepository->create([$easycredit], $context);
    }

    private function setPaymentMethodIsActive(bool $active, Context $context): void
    {
        /** @var EntityRepositoryInterface $paymentRepository */
        $paymentRepository = $this->container->get('payment_method.repository');
        $paymentMethodId = (new PaymentHelper($paymentRepository))->getPaymentMethodId($context);

        if ($paymentMethodId === null) {
            return;
        }

        $paymentMethod = [
            'id' => $paymentMethodId,
            'active' => $active,
        ];

        $paymentRepository->update([$paymentMethod], $context);
    }

    private function activateOrderTransactionCustomField(Context $context): void
    {
        /** @var EntityRepositoryInterface $customFieldRepository */
        $customFieldRepository = $this->container->get('custom_field.repository');
        $customFieldIds = $this->getCustomFieldIds($customFieldRepository, $context);

        if ($customFieldIds->getTotal() !== 0) {
            return;
        }

        $customFieldRepository->upsert(
            [
                [
                    'name' => self::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_ID,
                    'type' => CustomFieldTypes::TEXT,
                ],
            ],
            $context
        );
    }

    private function deactivateOrderTransactionCustomField(Context $context): void
    {
        /** @var EntityRepositoryInterface $customFieldRepository */
        $customFieldRepository = $this->container->get('custom_field.repository');
        $customFieldIds = $this->getCustomFieldIds($customFieldRepository, $context);

        if ($customFieldIds->getTotal() !== 0) {
            return;
        }

        $ids = [];
        foreach ($customFieldIds->getIds() as $customFieldId) {
            $ids[] = ['id' => $customFieldId];
        }
        $customFieldRepository->delete($ids, $context);
    }

    private function getCustomFieldIds(EntityRepositoryInterface $customFieldRepository, Context $context): IdSearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::ORDER_TRANSACTION_CUSTOM_FIELDS_EASYCREDIT_TRANSACTION_ID));

        return $customFieldRepository->searchIds($criteria, $context);
    }
}
