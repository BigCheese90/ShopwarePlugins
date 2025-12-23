<?php declare(strict_types=1);
namespace JakobShop\Storefront\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ExampleController extends StorefrontController
{

    private EntityRepository $manufacturerRepository;
    public function __construct(EntityRepository $manufacturerRepository)
    {
        $this->manufacturerRepository = $manufacturerRepository;
    }

    #[Route(path: '/documents/{name}', name: 'frontend.example.example', methods: ['GET'], defaults: ['_routeScope' => ['storefront']])]
    public function showPage(Request $request, Context $context, string $name): BinaryFileResponse
    {
        $filePath = "/var/www/sw6/custom/plugins/JakobShop/src/Resources/public/" . $name;
        //$filePath = __DIR__ . '/../../Resources/public/sample.pdf';
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, // or DISPOSITION_ATTACHMENT for download
            'allnet_sepa.pdf'
        );
        //$response = new BinaryFileResponse($request->server->get('HTTP_REFERER'));
        return $response;
        //this->renderStorefront('@JakobShop/page/index.html.twig');
    }

    #[Route(path: '/manufacturer', name: 'frontend.manufacturer.index', methods: ['GET'], defaults: ['_routeScope' => ['storefront']])]
    public function showIndex(Request $request, Context $context): Response
    {
        $manufacturers = $this->manufacturerRepository->search(new Criteria(), $context);
        return $this->renderStorefront('@JakobShop/storefront/page/manufacturers/index.html.twig',
            ["manufacturers" => $manufacturers]);
    }
}


