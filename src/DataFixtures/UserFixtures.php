<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger)
    {
        
    }
    private $counter = 1; //créer un compteur pour pouvoir utiliser user_id
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Factory::create('fr_FR');

        for($usr = 1; $usr <= 5; $usr++){
            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'user'));
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
            // $this->addReference('user-'.$this->counter, $user);
            // $this->counter++; 

        }

        $manager->flush();
    }

    // fonction qui évite redondance pour créer un user 
    public function createUser(string $username, ObjectManager $manager)
    {
        $username = new User();
        $username->setUsername($username);
        $manager->persist($username);

        $this->addReference(''.$this->counter, $username);
        $this->counter++;               //stock des ref que je vais rechercher sur TrickFixtures

        return $username;
    }
}
