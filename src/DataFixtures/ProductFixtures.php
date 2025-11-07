<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Service\ProductManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private $productManager;

    public function __construct(ProductManager $productManager) {
        $this->productManager = $productManager;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $name     = 'product '.$i;
            $storage  = 10;
            $category = $manager->getRepository(Category::class)->find(rand(1, 5));

            if (!$category) {
                continue;
            }

            $this->productManager->createProduct($name, $storage, $category);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
