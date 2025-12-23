<?php declare(strict_types=1);

namespace AdminTest\Backup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1749736256 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1749736256;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL

ALTER TABLE `producer_prices`
DROP COLUMN `manufacturer`
SQL;
        $connection->executeStatement($query);

    }
}
