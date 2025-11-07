<?php

// src/Service/ProductManager.php
namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function createProduct(string $name, int $storage, Category $category, ?float $unitPrice = null): Product
    {
        if ($unitPrice === null) {
            $unitPrice = mt_rand(10, 100);
        }

        $product = new Product();
        $product->setName($name);
        $product->setUnitPrice(number_format($unitPrice, 2, '.', ''));
        $product->setStorage($storage);
        $product->setCategory($category);
        $product->setCreatedAt(new \DateTime());

        $this->entityManager->persist($product);

        return $product;
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