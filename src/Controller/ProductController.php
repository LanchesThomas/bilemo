<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;
final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    /**
     * Récupère la liste des produits.
     *
     * Cette méthode permet de récupérer la liste des produits.
     * Par défaut, elle retourne 5 produits par page.
     * Il est possible de spécifier la page et le nombre d'éléments par page
     * en utilisant les paramètres de requête `page` et `limit`.
     */
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des produits',
    )]
    #[OA\Response(
        response: 404,
        description: 'Aucun produit trouvé'
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: "La page que l'on veut récupérer",
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: "Le nombre d'éléments que l'on veut récupérer",
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Product')]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);

        /** @var Customer $customer */
        $customer = $this->getUser();
        if (!$customer) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
        }
        $idCache = 'product_list_' . $customer->getId() . '_' . $page . "_" . $limit;


        $jsonProductList = $cachePool->get($idCache, function(ItemInterface $item) use ($productRepository, $page, $limit, $serializer) {
            $item->tag('productCache');
            $productList = $productRepository->findAllWithPagination($page, $limit);
            return $serializer->serialize($productList, 'json');
        });

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'detailProducts', methods: ['GET'])]
    /**
     * Récupère un produit par son ID.
     *
     * Cette méthode permet de récupérer un produit en fonction de son ID.
     */
    #[OA\Response(
        response: 200,
        description: 'Retourne le produit correspondant à l\'ID',
    )]
    #[OA\Response(
        response: 404,
        description: 'Produit non trouvé'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: "L'identifiant du produit à récupérer",
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Product')]
    public function getDetailProducts(Product $product, SerializerInterface $serializer): JsonResponse
    {
            $jsonProduct = $serializer->serialize($product, 'json');
            return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);  
    }
}
