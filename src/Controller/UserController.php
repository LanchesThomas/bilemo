<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    /**
     * Récupère la liste des utilisateurs liés à un client.
     *
     * Cette méthode permet de récupérer la liste des utilisateurs liés à un client.
     */
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des utilisateurs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Aucun utilisateur trouvé'
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
    #[OA\Tag(name: 'User')]
   public function getAllUsers(
        UserRepository $userRepository, 
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cachePool
    ): JsonResponse
{
    $page = $request->query->get('page', 1);
    $limit = $request->query->get('limit', 3);

    /** @var Customer $customer */
    $customer = $this->getUser();
    if (!$customer) {
        return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_UNAUTHORIZED);
    }

    $idCache = 'user_list_' . $customer->getId() . '_' . $page . '_' . $limit;

    // ⚠️ On va stocker "null" dans $jsonUserList si aucun utilisateur
    $jsonUserList = $cachePool->get($idCache, function(ItemInterface $item) use ($userRepository, $customer, $page, $limit, $serializer) {
        $item->tag('userCache');
        $userList = $userRepository->findAllbyCustomer($customer->getId(), $page, $limit);

        if (empty($userList)) {
            return null; // ⛔ Aucun utilisateur → on retourne null
        }

        $context = \JMS\Serializer\SerializationContext::create()->setGroups(['getUsers']);
        return $serializer->serialize($userList, 'json', $context);
    });

    if ($jsonUserList === null) {
        return new JsonResponse(['message' => 'Aucun utilisateur trouvé'], Response::HTTP_NOT_FOUND);
    }

    return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
}


#[Route('/api/users/{id}', name: 'usersDetails', methods: ['GET'])]
    /**
     * Récupère les détails d'un utilisateur.
     *
     * Cette méthode permet de récupérer les détails d'un utilisateur spécifique.
     */
    #[OA\Response(
        response: 200,
        description: 'Retourne les détails de l\'utilisateur',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Aucun utilisateur trouvé'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: "L'identifiant de l'utilisateur",
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'User')]

public function getDetailUsers(
    int $id, 
    UserRepository $userRepository, 
    SerializerInterface $serializer,
    TagAwareCacheInterface $cachePool
): JsonResponse {
    /** @var Customer $customer */
    $customer = $this->getUser();

    if (!$customer) {
        return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_UNAUTHORIZED);
    }

    $idCache = 'user_' . $id . '_' . $customer->getId();
    $jsonUser = $cachePool->get($idCache, function(ItemInterface $item) use ($serializer, $userRepository, $id, $customer) {
        $item->tag('userCache');

        $user = $userRepository->findUserByCustomer($id, $customer->getId());
        
        if (!$user) {
            return null;
        }

        $context = SerializationContext::create()->setGroups(['getUsers']);
        return $serializer->serialize($user, 'json', $context);
    });

    if ($jsonUser === null) {
        return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
    }

    return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
}


    #[Route('/api/users', name:"createUser", methods: ['POST'])]
    /**
     * Crée un nouvel utilisateur.
     *
     * Cette méthode permet de créer un nouvel utilisateur.
     */

    #[OA\RequestBody(
        description: 'Données de l\'utilisateur à créer',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'firstname', type: 'string', example: 'John'),
                new OA\Property(property: 'email', type: 'string', example:'johndoe@mail.com', description: 'Email de l\'utilisateur'),
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Erreur de validation',
    )]
    #[OA\Response(
        response: 404,
        description: 'Aucun utilisateur trouvé'
    )]

    #[OA\Tag(name: 'User')]
    public function createUser(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse 
    {
        // Désérialisation sans customer
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $customer = $this->getUser();
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        // Assigner le customer
        $user->setCustomer($customer);

        // On vérifie les erreurs
        $errors = $validator->validate($user); 

        if ($errors->count() > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
        
            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Enregistrer en base
        $em->persist($user);
        $em->flush();

        // Retourner la réponse
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate('usersDetails', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

   #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    /**
     * Supprime un utilisateur.
     *
     * Cette méthode permet de supprimer un utilisateur spécifique.
     */
    #[OA\Response(
        response: 204,
        description: 'Utilisateur supprimé avec succès'
    )]
    #[OA\Response(
        response: 404,
        description: 'Aucun utilisateur trouvé'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: "L'identifiant de l'utilisateur",
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'User')]
    public function deleteBook($id, User $user, EntityManagerInterface $em, UserRepository $userRepository, TagAwareCacheInterface $cache): JsonResponse 
    {
        /** @var Customer $customer */
        $customer = $this->getUser();

        if (!$customer) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
        }

        $user = $userRepository->findUserByCustomer($id, $customer->getId());
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
        } else {
            $cache->invalidateTags(['userCache']);
            $em->remove($user);
            $em->flush();
        }
        

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    
}
