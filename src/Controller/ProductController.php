<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
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
        $products = $productRepository->findAll();

        $json = $serializer->serialize($products, 'json', ['groups' => 'product:list']);

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
        $product = $productRepository->find($id);

        $json = $serializer->serialize($product, 'json', ['groups' => 'product:detail']);

        return new JsonResponse($json, 200, [], true);
    }
}
