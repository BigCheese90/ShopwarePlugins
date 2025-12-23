<?php declare(strict_types=1);

namespace AdminTest\Backup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1753266303 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1753266303;
    }

    public function update(Connection $connection): void
    {

    }
}
