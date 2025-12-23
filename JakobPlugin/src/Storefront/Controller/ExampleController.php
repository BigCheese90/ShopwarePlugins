<?php declare(strict_types=1);

namespace JakobPlugin\Storefront\Controller;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Delivery\DeliveryProcessor;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use JakobPlugin\Extension\Cart\PartialDeliveryCartExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
#[Route(defaults: ['_routeScope' => ['storefront']])]
class ExampleController extends StorefrontController
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(
        path: '/example',
        name: 'frontend.example.example',
        methods: ['GET']
    )]
    public function showExample(Request $request, SalesChannelContext $context, CartService $cartService): Response
    {
        $this->logger->debug('Partial delivery flag recorded for order', ["context" => "testdsaasdadsdsa"]);
        $cart = $cartService->getCart($context->getToken(), $context);
        $extension = new PartialDeliveryCartExtension(true);
        $cart->addExtension("Teillieferung", $extension);


        //$cart->addExtension(DeliveryProcessor::MANUAL_SHIPPING_COSTS, $calculatedPrice)
        //$this->data->set('Teillieferung', "1");
        return $this->renderStorefront('@JakobPlugin/storefront/page/example.html.twig', [
            'example' => 'Hello world'
            ,"cart" => $cart
        ]);
    }

    #[Route(
        path: '/checkout/cart/partial-delivery-on',
        name: 'frontend.checkout.cart.partial-delivery-on',
        methods: ['POST']
    )]
    public function partialDeliveryOn(Request $request, SalesChannelContext $context, CartService $cartService): Response
    {

        $cart = $cartService->getCart($context->getToken(), $context);
        $extension = new PartialDeliveryCartExtension(true);
        $cart->addExtension("Teillieferung", $extension);
        $cartService->recalculate($cart, $context);

        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    #[Route(
        path: '/checkout/cart/partial-delivery-off',
        name: 'frontend.checkout.cart.partial-delivery-off',
        methods: ['POST']
    )]
    public function partialDeliveryOff(Request $request, SalesChannelContext $context, CartService $cartService): Response
    {

        $cart = $cartService->getCart($context->getToken(), $context);
        $extension = new PartialDeliveryCartExtension(false);
        $cart->addExtension("Teillieferung", $extension);
        $cartService->recalculate($cart, $context);

        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }


}
