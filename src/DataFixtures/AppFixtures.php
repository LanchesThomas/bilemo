<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $customerList = [];
        // for ($i=0; $i < 5 ; $i++) { 
            $customer = new Customer();
            $customer->setUsername("admin");
            $customer->setEmail("admin@mail.com");

            $plainPassword = "admin-password";
            $hashedPassword = $this->passwordHasher->hashPassword($customer, $plainPassword);
            $customer->setPassword($hashedPassword);

            $manager->persist($customer);
            $customerList[] = $customer;

        // }

        for ($i=0; $i < 10 ; $i++) { 
            $user = new User();
            $user->setName("User_" .$i);
            $user->setFirstname("User_Firstname_" . $i);
            $user->setEmail("user_" . $i . "@mail.com");
            $user->setCustomer($customerList[array_rand($customerList)]);

            $manager->persist($user);

        }

        for ($i=0; $i < 20; $i++) { 
            $product = new Product();
            $product->setName("Téléphone_" . $i);
            $product->setColor("red");
            $product->setPrice(120);
            $product->setDescription(("Description_" . $i));
            $product->setBrand("Brand _" . $i);
            $product->setStock(10);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedat(new \DateTimeImmutable());   
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}
