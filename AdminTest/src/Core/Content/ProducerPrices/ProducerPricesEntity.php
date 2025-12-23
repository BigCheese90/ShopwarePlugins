<?php declare(strict_types=1);

namespace AdminTest\Core\Content\ProducerPrices;

use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\FieldType;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ForeignKey;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ManyToOne;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Entity as EntityAttribute;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\System\Tag\TagEntity;

#[EntityAttribute('producer_prices')]
class ProducerPricesEntity extends Entity
{
    #[PrimaryKey]
    #[Field(type: FieldType::UUID, api: [AdminApiSource::class])]
    public string $id;

    #[ForeignKey(entity: 'tag', api: [AdminApiSource::class])]
    public ?string $tagId = null;

    #[ManytoOne(entity: 'tag', api: [AdminApiSource::class])]
    public ?TagEntity $tag = null;

    #[ForeignKey(entity: 'product_manufacturer', api: [AdminApiSource::class])]
    public ?string $manufacturerId = null;

    #[ManytoOne(entity: 'product_manufacturer', api: [AdminApiSource::class])]
    public ?ProductManufacturerEntity $manufacturer = null;
/*    #[Field(type: FieldType::STRING, api: [AdminApiSource::class])]
    public string $manufacturer;*/

    #[Field(type: FieldType::FLOAT, api: [AdminApiSource::class])]
    public float $discount;

    #[Field(type: FieldType::STRING)]
    public ?string $priceReference= null;

    #[Field(type: FieldType::STRING)]
    public ?string $comment= null;

    #[Field(type: FieldType::STRING)]
    public ?string $userName= null;


}