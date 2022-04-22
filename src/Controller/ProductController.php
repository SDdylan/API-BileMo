<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
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
     *     path="/api/products",
     *     @OA\Response(
     *          response="200",
     *          description="Liste des produits",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product")),
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas"),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     */
    public function listProduct(ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $hateoas = HateoasBuilder::create()->build();

        $products = $productRepository->findAll();

        $json = $hateoas->serialize($products, 'json', SerializationContext::create()->setGroups(array('product:list')));

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID de la ressource.",
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
     * @Route("/api/products/{id}", name="api_product_detail", methods={"GET"})
     */
    public function detailProduct(int $id, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $hateoas = HateoasBuilder::create()->build();

        $product = $productRepository->find($id);

        $json = $hateoas->serialize($product, 'json', SerializationContext::create()->setGroups(array('product:detail')));

        return new JsonResponse($json, 200, [], true);
    }
}
