<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Netzkollektiv\EasyCredit\Payment\Handler;

class Migration1661846378BrandRelaunch extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661846378;
    }

    protected function _replace($column) {
        return \sprintf("%s = REPLACE(%s, 'ratenkauf by easyCredit', 'easyCredit-Ratenkauf')", $column, $column);
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate("
            UPDATE payment_method_translation pmt INNER JOIN payment_method pm ON pm.id = pmt.payment_method_id Set 
                {$this->_replace('pmt.name')},
                {$this->_replace('pmt.distinguishable_name')},
                {$this->_replace('pmt.description')}
            WHERE pm.handler_identifier = :handler;
        ", ['handler' => Handler::class]);

        $connection->executeUpdate("
            UPDATE plugin_translation pt INNER JOIN plugin p ON p.id = pt.plugin_id Set
                {$this->_replace('pt.label')}
            WHERE p.name = 'EasyCreditRatenkauf';
        ");

	$connection->executeUpdate(" 
            UPDATE rule r INNER JOIN payment_method pm ON r.id = pm.availability_rule_id Set
                {$this->_replace('r.name')},
                {$this->_replace('r.description')}
            WHERE pm.handler_identifier = :handler;
        ", ['handler' => Handler::class]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
