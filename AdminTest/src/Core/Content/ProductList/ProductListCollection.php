<?php declare(strict_types=1);

namespace AdminTest\Core\Content\ProductList;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ProductListEntity $entity)
 * @method void set(string $key, ProductListEntity $entity)
 * @method ProductListEntity[] getIterator()
 * @method ProductListEntity[] getElements()
 * @method ProductListEntity|null get(string $key)
 * @method ProductListEntity|null first()
 * @method ProductListEntity|null last()
 */
class ProductListCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductListEntity::class;
    }
}
