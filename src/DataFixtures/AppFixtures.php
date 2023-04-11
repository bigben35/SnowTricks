<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Contact;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Contact
        $faker = Factory::create('fr_FR');

        for ($c = 1; $c <= 5; $c++) {
            $contact = new Contact();
            $contact->setLastname($faker->name())
                ->setFirstname($faker->firstName())
                ->setEmail($faker->email())
                ->setObject('Demande n°' . ($c + 1))
                ->setMessage($faker->text());

                $manager->persist($contact); 
            }

        $manager->flush();
    }
}
