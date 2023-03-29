<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private $counter = 1; //créer un compteur pour pouvoir utiliser category_id
    public function load(ObjectManager $manager): void
    {
        $this->createCategory('180', $manager);
        $this->createCategory('360', $manager);
        $this->createCategory('540', $manager);
        $this->createCategory('720', $manager);
        $this->createCategory('1080', $manager);

        $manager->flush();
    }

    // fonction qui évite redondance pour créer une catégorie 
    public function createCategory(string $name, ObjectManager $manager)
    {
        $category = new Category();
        $category->setName($name);
        $manager->persist($category);

        $this->addReference('category-' . $this->counter, $category);
        $this->counter++;               //stock des ref que je vais rechercher sur TrickFixtures

        return $category;
    }
}
