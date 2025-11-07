<?php

// src/Service/ProductManager.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;

class ProductManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function createProduct(string $name, int $storage, Category $category, $unitPrice = null) : bool
    {
        if (!$unitPrice) {
            // création de produit depuis userFixtures
            $unitPrice  = mt_rand(10, 100);
        }

        $product = new Product();
        $product->setName($name);
        $product->setUnitPrice($unitPrice);
        $product->setCreatedAt(new \Datetime());
        // ... to be completed
        $this->entityManager->persist($product);
    }

    public function getHappyMessage(): string
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}