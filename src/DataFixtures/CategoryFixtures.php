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
        $category = $this->createCategory('180', $manager);
        $category = $this->createCategory('360', $manager);
        $category = $this->createCategory('540', $manager);
        $category = $this->createCategory('720', $manager);
        $category = $this->createCategory('1080', $manager);


        // $category = new Category();
        // $category->setName('360');
        // $manager->persist($category);

        // $category = new Category();
        // $category->setName('540');
        // $manager->persist($category);

        // $category = new Category();
        // $category->setName('720');
        // $manager->persist($category);

        // $category = new Category();
        // $category->setName('1080');
        // $manager->persist($category);

        $manager->flush();
    }

    // fonction qui évite redondance pour créer une catégorie 
    public function createCategory(string $name, ObjectManager $manager)
    {
        $category = new Category();
        $category->setName($name);
        $manager->persist($category);

        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;               //stock des ref que je vais rechercher sur TrickFixtures

        return $category;
    }
}
