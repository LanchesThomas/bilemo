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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getAllUsers(
        UserRepository $userRepository, 
        SerializerInterface $serializer
        ): JsonResponse
    {
        $userList = $userRepository->findAllbyCustomer(1);
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'usersDetails', methods: ['GET'])]
    public function getDetailUsers(
        int $id, 
        UserRepository $userRepository, 
        SerializerInterface $serializer
        ): JsonResponse
    {
        $user = $userRepository->findUserByCustomer($id, 1);

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé ou accès interdit'], Response::HTTP_NOT_FOUND);
        }

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users', name:"createUser", methods: ['POST'])]
    public function createUser(
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $em, 
        UrlGeneratorInterface $urlGenerator,
        CustomerRepository $customerRepository,
        ValidatorInterface $validator
    ): JsonResponse 
    {
        // Désérialisation sans customer
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // Récupérer le customer (l'ID est passé en JSON)
        $data = json_decode($request->getContent(), true);
        if (!isset($data['customer']['id'])) {
            return new JsonResponse(['error' => 'Customer ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $customer = $customerRepository->find($data['customer']['id']);
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        // Assigner le customer
        $user->setCustomer($customer);

        // On vérifie les erreurs
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        // Enregistrer en base
        $em->persist($user);
        $em->flush();

        // Retourner la réponse
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        $location = $urlGenerator->generate('usersDetails', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/users/{id}', name:"updateBook", methods:['PUT'])]

    public function updateBook(
        Request $request, 
        SerializerInterface $serializer, 
        User $currentUser, 
        EntityManagerInterface $em, 
        CustomerRepository $customerRepository
        ): JsonResponse 
    {
        $updatedUser = $serializer->deserialize($request->getContent(), 
                User::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        // Récupérer le customer (l'ID est passé en JSON)
        $data = json_decode($request->getContent(), true);
        if (!isset($data['customer']['id'])) {
            return new JsonResponse(['error' => 'Customer ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $customer = $customerRepository->find($data['customer']['id']);
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        $updatedUser->setCustomer($customerRepository->find($customer));
        
        $em->persist($updatedUser);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
   }

   #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteBook(User $user, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    
}
