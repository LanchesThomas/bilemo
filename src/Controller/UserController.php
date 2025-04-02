<?php

namespace App\Controller;

use App\Entity\Customer;
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

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->findAllbyCustomer(1);
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}', name: 'usersDetails', methods: ['GET'])]
    public function getDetailUsers(int $id, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
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
        CustomerRepository $customerRepository
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

        // Enregistrer en base
        $em->persist($user);
        $em->flush();

        // Retourner la réponse
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        $location = $urlGenerator->generate('usersDetails', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }



    
}
