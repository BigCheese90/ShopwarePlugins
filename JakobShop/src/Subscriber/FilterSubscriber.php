<?php

namespace JakobShop\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCollectFilterEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\FilterAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MaxAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingCollectFilterEvent::class => 'addInStockFilter'
        ];
    }

    public function addInStockFilter(ProductListingCollectFilterEvent $event): void
    {
        $filters = $event->getFilters();
        $request = $event->getRequest();
        $isInStockFiltered = (bool) $request->get('is-in-stock');

        $isInStockFilter = new Filter(
            'is-in-stock',
            $isInStockFiltered,
            [
                new FilterAggregation(
                    'is-in-stock-filter',
                    new MaxAggregation('is-in-stock', 'product.stock'),
                    [
                        new RangeFilter('product.stock', [RangeFilter::GT => 0])
                    ]
                ),
            ],
            new RangeFilter('product.stock', [RangeFilter::GT => 0]),
            $isInStockFiltered
        );

        $filters->add($isInStockFilter);
    }
}