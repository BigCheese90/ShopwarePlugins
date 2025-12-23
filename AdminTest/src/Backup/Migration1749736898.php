<?php declare(strict_types=1);

namespace AdminTest\Backup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1749736898 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1749736898;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL

ALTER TABLE `producer_prices`
ADD CONSTRAINT `fk.producer_prices.manufacturer`
FOREIGN KEY (`manufacturer_id`)
REFERENCES `product_manufacturer` (`id`)
ON DELETE CASCADE
ON UPDATE CASCADE;
SQL;
        $connection->executeStatement($query);
    }
}
