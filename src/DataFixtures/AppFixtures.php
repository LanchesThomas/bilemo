<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
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
