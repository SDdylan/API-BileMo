<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="api_product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $products = $productRepository->findAll();

        foreach ($products as $product){
            $array = [
                'type' => "phone"
            ];
            array_push($product, $array);
        }

        $json = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

//        foreach($json as $object){
//            $array = [
//                'type' => "phone"
//            ];
//            $array = json_encode($array);
//            array_push($object, $array);
//        }

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        $productNormalize = $this->json($products, 200, [], ['groups' => 'product:read']);

        $json = json_encode($productNormalize);

        dd($json);

        return $this->json($productRepository->findAll(), 200, []);
    }
}
