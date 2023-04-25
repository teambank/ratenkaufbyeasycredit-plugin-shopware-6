<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1637924278ConfigStatus extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1_637_924_278;
    }

    public function update(Connection $connection): void
    {
        if (!$this->configKeyExists($connection, 'orderStatus')) {
            $orderStateId = $this->getStateId(
                $connection,
                ['state_machine' => 'order.state', 'state_machine_state' => 'open']
            );

            $connection->insert('system_config', [
                'id' => Uuid::randomBytes(),
                'configuration_key' => 'EasyCreditRatenkauf.config.orderStatus',
                'configuration_value' => \sprintf('{"_value": "%s"}', Uuid::fromBytesToHex($orderStateId)),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }

        if (!$this->configKeyExists($connection, 'paymentStatus')) {
            $paymentStateId = $this->getStateId(
                $connection,
                ['state_machine' => 'order_transaction.state', 'state_machine_state' => 'authorized']
            );

            $connection->insert('system_config', [
                'id' => Uuid::randomBytes(),
                'configuration_key' => 'EasyCreditRatenkauf.config.paymentStatus',
                'configuration_value' => \sprintf('{"_value": "%s"}', Uuid::fromBytesToHex($paymentStateId)),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    protected function configKeyExists($connection, $key)
    {
        return $connection->fetchOne(
            'SELECT id FROM system_config WHERE configuration_key = :configuration_key',
            ['configuration_key' => 'EasyCreditRatenkauf.config.' . $key]
        );
    }

    protected function getStateId($connection, $params)
    {
        return $connection->fetchOne(
            '
            SELECT sms.id FROM state_machine_state sms
                LEFT JOIN state_machine sm ON sms.state_machine_id = sm.id WHERE
	            sms.technical_name = :state_machine_state AND sm.technical_name = :state_machine',
            $params
        );
    }
}
