<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ['GET'])]
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
            return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
        }

        $idCache = 'user_list_' . $customer->getId() . '_' . $page . '_' . $limit;
        // On récupère tous les utilisateurs liés au customer 
        // On utilise le cache pour éviter de faire trop de requêtes
        $jsonUserList = $cachePool->get($idCache, function(ItemInterface $item) use ($userRepository, $customer, $page, $limit, $serializer) {
            echo "Element pas en cache"; // Debug message 
            $item->tag('userCache');
            $userList = $userRepository->findAllbyCustomer($customer->getId(), $page, $limit);
            return $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);
        });
       
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'usersDetails', methods: ['GET'])]
    public function getDetailUsers(
        int $id, 
        UserRepository $userRepository, 
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool
        ): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $this->getUser();

        if (!$customer) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
        }

        $idCache = 'user_' . $id . '_' . $customer->getId();
        // On récupère tous les utilisateurs liés au customer
        // On utilise le cache pour éviter de faire trop de requêtes
        $jsonUser = $cachePool->get($idCache, function(ItemInterface $item) use ($userRepository, $customer, $id, $serializer) {
            echo "Element pas en cache"; // Debug message 
            $item->tag('userCache');
            $user = $userRepository->findUserByCustomer($id, $customer->getId());
            if (!$user) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
            }
            return $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        });
        
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users', name:"createUser", methods: ['POST'])]
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
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        $location = $urlGenerator->generate('usersDetails', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

   #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
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
