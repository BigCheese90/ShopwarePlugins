<?php declare(strict_types=1);

namespace JakobPlugin\Core\Content\Test;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(TestEntity $entity)
 * @method void set(string $key, TestEntity $entity)
 * @method TestEntity[] getIterator()
 * @method TestEntity[] getElements()
 * @method TestEntity|null get(string $key)
 * @method TestEntity|null first()
 * @method TestEntity|null last()
 */
class TestCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TestEntity::class;
    }
}
