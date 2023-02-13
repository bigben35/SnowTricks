<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Trick;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
// use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger, private UserRepository $userRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generatot instance
        $faker = Faker\Factory::create('fr_FR');

        for ($trick = 1; $trick <= 10; $trick++) {
            $tricks = new Trick();
            $tricks->setName($faker->text(5));
            // $tricks->setAuthor($faker->text(5));
            // $user = $this->getReference(1, 6);
            // $tricks->setAuthor($faker->name());
            $tricks->setDescription($faker->text());
            $tricks->setSlug($this->slugger->slug($tricks->getName())->lower());
            // $tricks->setCategory($category);
            //on va chercher une réf de catégorie
            $category = $this->getReference('' . rand(1, 5));
            $tricks->addCategory($category);
            $tricks->setIllustration($faker->imageUrl());
            $tricks->setVideo($faker->text(5));

            // $user = $this->getReference('' . rand(1, 5));
            $tricks->setUser($this->userRepository->findOneByEmail('admin@admin.fr')->getId());
            $manager->persist($tricks);
        }

        $manager->flush();
    }
}
