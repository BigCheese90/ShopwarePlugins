<?php declare(strict_types=1);

namespace AdminTest\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1748724987FirstMigration extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1748724987;
    }

    public function update(Connection $connection): void
    {

    }
}
