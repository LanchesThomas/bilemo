<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productRepository->findAll();
        $jsonProductList = $serializer->serialize($productList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'detailProducts', methods: ['GET'])]
    public function getDetailProducts(Product $product, SerializerInterface $serializer): JsonResponse
    {
            $jsonProduct = $serializer->serialize($product, 'json');
            return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);  
    }
}
