<?php

namespace AdminTest\Core\Content\DiscountProducts;


use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\FieldType;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ForeignKey;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ManyToOne;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ReferenceVersion;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Entity as EntityAttribute;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\System\Tag\TagEntity;


#[EntityAttribute('discount_products')]
class DiscountProductsEntity extends Entity
{
    #[PrimaryKey]
    #[Field(type: FieldType::UUID, api: [AdminApiSource::class])]
    public string $id;

    #[ForeignKey(entity: 'tag', api: [AdminApiSource::class])]
    public ?string $tagId = null;

    #[ManytoOne(entity: 'tag', api: [AdminApiSource::class])]
    public ?TagEntity $tag = null;

    #[ForeignKey(entity: 'product', api: [AdminApiSource::class])]
    public ?string $productId = null;

    #[ReferenceVersion(entity: 'product')]
    public ?string $productVersionId = null;

    #[ManytoOne(entity: 'product',  api: [AdminApiSource::class])]
    public ?ProductEntity $product = null;
    #[Field(type: FieldType::FLOAT, api: [AdminApiSource::class])]
    public ?float $fixedPrice = null;

    #[Field(type: FieldType::FLOAT, api: [AdminApiSource::class])]
    public ?float $discount = null;

    #[Field(type: FieldType::STRING)]
    public ?string $priceReference= null;

    #[Field(type: FieldType::STRING)]
    public ?string $comment = null;

    #[Field(type: FieldType::STRING)]
    public ?string $userName = null;

}