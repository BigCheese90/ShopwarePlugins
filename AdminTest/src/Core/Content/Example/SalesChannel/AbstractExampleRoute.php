<?php declare(strict_types=1);

namespace AdminTest\Core\Content\Example\SalesChannel;

use AdminTest\Core\Content\Example\SalesChannel\ExampleRouteResponse;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractExampleRoute
{
    abstract public function getDecorated(): AbstractExampleRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context): ExampleRouteResponse;
}
