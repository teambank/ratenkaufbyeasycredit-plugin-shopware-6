<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1603803110UpdateSettings extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1603803110;
    }

    public function update(Connection $connection): void
    {
        $sql = "UPDATE system_config Set configuration_key = REPLACE(configuration_key, '.settings.', '.config.') WHERE configuration_key LIKE 'EasyCreditRatenkauf.settings%'";
        $connection->executeUpdate($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
