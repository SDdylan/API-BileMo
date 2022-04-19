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

//use JMS\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     */
    public function listProduct(ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $hateoas = HateoasBuilder::create()->build();

        $products = $productRepository->findAll();

        $json = $hateoas->serialize($products, 'json', SerializationContext::create()->setGroups(array('product:list')));

        return new JsonResponse($json, 200, [], true);
//        return new JsonResponse($this->serializer->serialize($products, 'json', SerializationContext::create()->setGroups(array('product:list'))),
//            JsonResponse::HTTP_OK,
//            [],
//            true
//        );
    }

    /**
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
