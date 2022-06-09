<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Util\Lifecycle;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Checkout\Customer\Rule\BillingCountryRule;
use Shopware\Core\Framework\Rule\Container\AndRule;
use Shopware\Core\System\Currency\Rule\CurrencyRule;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\System\Currency\CurrencyDefinition;
use Netzkollektiv\EasyCredit\Helper\Payment as PaymentHelper;
use Netzkollektiv\EasyCredit\Payment\Handler;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsService;
use Netzkollektiv\EasyCredit\Setting\SettingStruct;

class InstallUninstall
{
    /**
     * @var EntityRepositoryInterface
     */
    private $systemConfigRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $countryRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var PluginIdProvider
     */
    private $pluginIdProvider;

    /**
     * @var string
     */
    private $className;

    /**
     * @var SystemConfigService
     */
    private $systemConfig;

    public function __construct(
        EntityRepositoryInterface $systemConfigRepository,
        EntityRepositoryInterface $paymentRepository,
        EntityRepositoryInterface $salesChannelRepository,
        EntityRepositoryInterface $ruleRepository,
        EntityRepositoryInterface $countryRepository,
        EntityRepositoryInterface $currencyRepository,
        PluginIdProvider $pluginIdProvider,
        SystemConfigService $systemConfig,
        string $className
    ) {
        $this->systemConfigRepository = $systemConfigRepository;
        $this->paymentRepository = $paymentRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->ruleRepository = $ruleRepository;
        $this->countryRepository = $countryRepository;
        $this->currencyRepository = $currencyRepository;
        $this->pluginIdProvider = $pluginIdProvider;
        $this->className = $className;
        $this->systemConfig = $systemConfig;
    }

    public function install(Context $context): void
    {
        $this->addDefaultConfiguration($context);
        $this->addPaymentMethods($context);
    }

    public function uninstall(Context $context): void
    {
        $this->removeConfiguration($context);
    }

    private function addDefaultConfiguration(Context $context): void
    {
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('configurationKey', SettingsService::SYSTEM_CONFIG_DOMAIN));
        $existingSettings = $this->systemConfigRepository->search($criteria, $context);

        foreach ((new SettingStruct())->jsonSerialize() as $key => $value) {
            if ($value === null || $value === []) {
                continue;
            }

            $fullKey = SettingsService::SYSTEM_CONFIG_DOMAIN . $key;

            $sytemConfigCollection = $existingSettings->filter(function ($item) use ($fullKey) {
                return $item->getConfigurationKey() === $fullKey;
            })->getEntities();

            if (\count($sytemConfigCollection) === 0) {
                $this->systemConfig->set($fullKey, $value);
            }
        }
    }

    private function removeConfiguration(Context $context): void
    {
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('configurationKey', SettingsService::SYSTEM_CONFIG_DOMAIN));
        $idSearchResult = $this->systemConfigRepository->searchIds($criteria, $context);

        $ids = \array_map(static function ($id) {
            return ['id' => $id];
        }, $idSearchResult->getIds());

        $this->systemConfigRepository->delete($ids, $context);
    }

    private function addPaymentMethods(Context $context): void
    {
        $pluginId = $this->pluginIdProvider->getPluginIdByBaseClass($this->className, $context);
        $paymentHelper = new PaymentHelper(
            $this->paymentRepository,
            $this->salesChannelRepository
        );

        $data = [
            'handlerIdentifier' => Handler::class,
            'name' => 'ratenkauf by easyCredit',
            'position' => -100,
            'pluginId' => $pluginId,
            'translations' => [
                'de-DE' => [
                    'description' => 'ratenkauf by easyCredit - Einfach. Fair. In Raten zahlen.',
                ],
                'en-GB' => [
                    'description' => 'ratenkauf by easyCredit - Easy. Fair. Pay by installments.',
                ],
            ],
            'availabilityRule' => [
                'name' => 'ratenkauf by easyCredit - nur verfügbar in DE, bei Zahlung in EUR',
                'priority' => 1,
                'description' => 'Diese Verfügbarkeitsregel wurde automatisch bei Installation von ratenkauf by easyCredit erstellt. Sie kann beliebig angepasst werden und bei Updates nicht überschrieben.',
                'conditions' => [
                    [
                        'type' => (new AndRule())->getName(),
                        'children' => [
                            [
                                'type' => (new BillingCountryRule())->getName(),
                                'value' => [
                                    'operator' => BillingCountryRule::OPERATOR_EQ,
                                    'countryIds' => $this->getCountryIds(['DE'], $context),
                                ],
                            ],
                            [
                                'type' => (new CurrencyRule())->getName(),
                                'value' => [
                                    'operator' => CurrencyRule::OPERATOR_EQ,
                                    'currencyIds' => $this->getCurrencyIds(['EUR'], $context),
                                ],
                            ]
                        ],
                    ],
                ],
            ]
        ];

        $paymentMethodId = $paymentHelper->getPaymentMethodId($context);
        if ($paymentMethodId !== null) {
            $data['id'] = $paymentMethodId;
        }

        $this->paymentRepository->upsert([$data], $context);
    }

    protected function getCountryIds(array $countryIsos, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('iso', $countryIsos));

        /** @var string[] $countryIds */
        $countryIds = $this->countryRepository->searchIds($criteria, $context)->getIds();

        if (empty($countryIds)) {
            // if country does not exist, enter invalid uuid so rule always fails. empty is not allowed
            return [Uuid::randomHex()];
        }

        return $countryIds;
    }

    protected function getCurrencyIds(array $currencyCodes, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('isoCode', $currencyCodes));

        /** @var string[] $currencyIds */
        $currencyIds = $this->currencyRepository->searchIds($criteria, $context)->getIds();

        if (empty($currencyIds)) {
            // if currency does not exist, enter invalid uuid so rule always fails. empty is not allowed
            return [Uuid::randomHex()];
        }

        return $currencyIds;
    }
}
