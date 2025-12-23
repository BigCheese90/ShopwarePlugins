<?php declare(strict_types=1);

namespace AdminTest\Service;

use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use function Symfony\Component\VarDumper\dump;
class CustomProductPriceCalculator extends AbstractProductPriceCalculator
{
    /**
     * @var AbstractProductPriceCalculator
     */

    private AbstractProductPriceCalculator $productPriceCalculator;
    private EntityRepository $manufacturerDiscountRepository;
    private EntityRepository $productDiscountRepository;

    public function __construct(AbstractProductPriceCalculator $productPriceCalculator,
                                EntityRepository $manufacturerDiscountRepository,
                                EntityRepository $productDiscountRepository)
    {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->manufacturerDiscountRepository = $manufacturerDiscountRepository;
        $this->productDiscountRepository = $productDiscountRepository;
    }

    public function getDecorated(): AbstractProductPriceCalculator
    {
        return $this->productPriceCalculator;
    }

    public function calculate(iterable $products, SalesChannelContext $context): void
    {
        $customer = $context->getCustomer();
        if (!$customer) {

            $this->getDecorated()->calculate($products, $context);
            return;
        }
        $tags = $customer->getTagIds();
        if ($tags === null) {
            $tags = [];
        }
        /*$tags[] = null;*/

        //dump($tags);

        $criteria = new Criteria();
        $criteria->addFilter(new Orfilter([
            new EqualsAnyFilter('tagId', $tags),
            new EqualsFilter('tagId', null)
        ]));
        $discount_products_entity = $this->productDiscountRepository->search($criteria, Context::createDefaultContext())->getEntities();
        $discount_products = [];
        foreach($discount_products_entity as $discountProduct) {
            $discount_products[] = [$discountProduct->get("productId"), $discountProduct->get("discount"), $discountProduct->get("fixedPrice")];
        }


        $criteria = new Criteria();
        $criteria->addFilter(new Orfilter([
            new EqualsAnyFilter('tagId', $tags),
            new EqualsFilter('tagId', null)
    ]));
        $discount_manufacturer_entity = $this->manufacturerDiscountRepository->search($criteria, Context::createDefaultContext())->getEntities();
        $discount_manufacturers = [];
        foreach ($discount_manufacturer_entity as $discount) {
            $discount_manufacturers[] = [$discount->get("manufacturerId"), $discount->get("discount"), $discount->get("priceReference")];
        }
        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            if ($product->hasExtension("customPricecalculated")) { continue; }
            $productId = $product->getId();
            foreach ($discount_products as $discount) {
                if ($discount[0] == $productId) {
                    if ($discount[2] != null) {
                        $product->getPrice()->first()->setNet($discount[2]);
                        continue;
                    };
                    $price = $product->getPrice();
                    $price->first()->setNet($price->first()->getNet() * $discount[1]);
                }
            }


            $manufacturerId = $product->getManufacturerId();
            foreach ($discount_manufacturers as $discount) {
                if ($discount[0] == $manufacturerId) {
                  
                    $price = $product->getPrice();

                    if ($discount[2] !== null and $product->getTranslatedCustomFieldsValue($discount[2]) !== null) {
                        $new_price = $product->getTranslatedCustomFieldsValue($discount[2])->first()->getNet() * $discount[1];
                        $price->first()->setNet($new_price);


                        continue;
                    }

                    $price->first()->setNet($price ->first()->getNet() * $discount[1]);

                }
                $product->addExtension("customPricecalculated", new ArrayStruct([]));

            }




            // Just an example!
            // A product can have more than one price, which you also have to consider.
            // Also you might have to change the value of "getCheapestPrice"!
            /*$price->first()->setGross(100);*/

        }

        $this->getDecorated()->calculate($products, $context);
    }
}