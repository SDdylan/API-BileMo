<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
use Hateoas\HateoasBuilder;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use JMS\Serializer\Exception\Exception;
use JMS\Serializer\Exception\SkipHandlerException;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Hateoas\Representation\Factory\PagerfantaFactory;

//use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @OA\Info(title="API BileMo", version="0.1")
 * @OA\Server(
 *     url="/",
 *     description="Api BileMo"
 * )
 *
 * @OA\SecurityScheme(
 *      bearerFormat="JWT",
 *      securityScheme="bearer",
 *      type="apiKey",
 *      in="header",
 *      name="bearer",
 * )
 *
 **/
class ProductController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api/products/{page}",
     *     operationId="App\Controller\ProductController::listProduct",
     *     security={"bearer"},
     *     summary="Récupération de la liste des produits",
     *     tags={"Produits"},
     *     @OA\Parameter(
     *          name="page",
     *          in="path",
     *          description="Numéro de la page dans la liste des produits, on affiche 5 produits à partir de cet indicateur. (La première page est 0)",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Liste des produits",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product")),
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas"),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
     * @Route("/api/products/{page}", name="api_product_list", methods={"GET"})
     */
    public function listProduct(int $page, ProductRepository $productRepository, Request $request)
    {
        $hateoas = HateoasBuilder::create()->build();

        //$offset = max(0, $request->query->getInt('page', 0));
        $paginator = $productRepository->getProductPaginator($page);
        //$products = $productRepository->findAll();
        //dd($paginator->getQuery());
        //$products = $productRepository->getProductPaginator(1);

        $json = $hateoas->serialize($paginator->getQuery(), 'json', SerializationContext::create()->setGroups(array('product:list')));

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     operationId="App\Controller\ProductController::detailProduct",
     *     security={"bearer"},
     *     summary="Détail d'un produits",
     *     tags={"Produits"},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Identifiant du Produit.",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Le détail d'un produit",
     *          @OA\JsonContent(@OA\Items(ref="#/components/schemas/Product")),
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas"),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
     * @Route("/api/product/{id}", name="api_product_detail", methods={"GET"})
     */
    public function detailProduct(int $id, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $hateoas = HateoasBuilder::create()->build();

        $product = $productRepository->find($id);
        try {
            $json = $hateoas->serialize($product, 'json', SerializationContext::create()->setGroups(array('product:detail')));
            return new JsonResponse($json, 200, [], true);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], 400);
        }
    }
}