<?php

namespace JakobPlugin\Storefront\Controller;


use JakobPlugin\Extension\Cart\OrderReferenceCartExtension;
use JakobPlugin\Extension\Cart\PartialDeliveryCartExtension;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\SalesChannel\AbstractCartOrderRoute;
use Shopware\Core\Checkout\Cart\SalesChannel\CartOrderRouteResponse;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Storefront\Controller\CheckoutController;
use Shopware\Core\Checkout\Cart\SalesChannel\CartOrderRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutOrderDecorator extends CartOrderRoute
{
    private AbstractCartOrderRoute $decoratedCartOrderRoute;
    private LoggerInterface $logger;

    public function __construct(AbstractCartOrderRoute $decoratedCartOrderRoute,  LoggerInterface $logger)
    {

        $this->decoratedCartOrderRoute = $decoratedCartOrderRoute;
        $this->logger = $logger;
    }

    public function getDecorated(): AbstractCartOrderRoute
    {
        return $this->decoratedCartOrderRoute;
    }

    public function order(Cart $cart, SalesChannelContext $context, RequestDatabag $data): CartOrderRouteResponse
    {

        $text = $data->get("testinput");
        $this->logger->debug('Please', ["Work" => $text]);
        if ($text === null) {
            return $this->decoratedCartOrderRoute->order($cart,  $context, $data);
        }
        $extension = new OrderReferenceCartExtension($text);
        $cart->addExtension("orderReference", $extension);


        return $this->decoratedCartOrderRoute->order($cart,  $context, $data);

    }

}