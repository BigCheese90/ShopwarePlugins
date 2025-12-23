<?php declare(strict_types=1);

namespace AdminTest\Core\Content\ProductFeatures;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ProductFeaturesEntity $entity)
 * @method void set(string $key, ProductFeaturesEntity $entity)
 * @method ProductFeaturesEntity[] getIterator()
 * @method ProductFeaturesEntity[] getElements()
 * @method ProductFeaturesEntity|null get(string $key)
 * @method ProductFeaturesEntity|null first()
 * @method ProductFeaturesEntity|null last()
 */
class ProductFeaturesCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductFeaturesEntity::class;
    }
}
