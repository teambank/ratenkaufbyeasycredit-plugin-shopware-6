<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Util\Lifecycle;

use Netzkollektiv\EasyCredit\Helper\PaymentIdProvider;
use Netzkollektiv\EasyCredit\Payment\Handler;
use Netzkollektiv\EasyCredit\Setting\Exception\SettingsInvalidException;
use Netzkollektiv\EasyCredit\Setting\Service\SettingsService;
use Netzkollektiv\EasyCredit\Setting\SettingStruct;
use Netzkollektiv\EasyCredit\Setting\SettingStructValidator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;
use Shopware\Core\System\SystemConfig\SystemConfigService;

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
        PluginIdProvider $pluginIdProvider,
        SystemConfigService $systemConfig,
        string $className
    ) {
        $this->systemConfigRepository = $systemConfigRepository;
        $this->paymentRepository = $paymentRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->ruleRepository = $ruleRepository;
        $this->countryRepository = $countryRepository;
        $this->pluginIdProvider = $pluginIdProvider;
        $this->className = $className;
        $this->systemConfig = $systemConfig;
    }

    public function install(Context $context): void
    {
        $this->addDefaultConfiguration();
        $this->addPaymentMethods($context);
    }

    public function uninstall(Context $context): void
    {
        $this->removeConfiguration($context);
    }

    private function addDefaultConfiguration(): void
    {
        if ($this->validSettingsExists()) {
            return;
        }

        foreach ((new SettingStruct())->jsonSerialize() as $key => $value) {
            if ($value === null || $value === []) {
                continue;
            }
            $this->systemConfig->set(SettingsService::SYSTEM_CONFIG_DOMAIN . $key, $value);
        }
    }

    private function removeConfiguration(Context $context): void
    {
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('configurationKey', SettingsService::SYSTEM_CONFIG_DOMAIN));
        $idSearchResult = $this->systemConfigRepository->searchIds($criteria, $context);

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $idSearchResult->getIds());

        $this->systemConfigRepository->delete($ids, $context);
    }

    private function addPaymentMethods(Context $context): void
    {
        $pluginId = $this->pluginIdProvider->getPluginIdByBaseClass($this->className, $context);
        $paymentIdProvider = new PaymentIdProvider($this->paymentRepository);

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
        ];

        $paymentMethodId = $paymentIdProvider->getPaymentMethodId($context);
        if ($paymentMethodId !== null) {
            $data['id'] = $paymentMethodId;
        }

        $this->paymentRepository->upsert([$data], $context);
    }

    private function validSettingsExists(): bool
    {
        $keyValuePairs = $this->systemConfig->getDomain(SettingsService::SYSTEM_CONFIG_DOMAIN);

        $structData = [];
        foreach ($keyValuePairs as $key => $value) {
            $identifier = (string) mb_substr($key, \mb_strlen(SettingsService::SYSTEM_CONFIG_DOMAIN));
            if ($identifier === '') {
                continue;
            }
            $structData[$identifier] = $value;
        }

        $settings = (new SettingStruct())->assign($structData);

        try {
            SettingStructValidator::validate($settings);
        } catch (SettingsInvalidException $e) {
            return false;
        }

        return true;
    }
}
