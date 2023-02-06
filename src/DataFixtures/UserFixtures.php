<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger,
        private UserRepository $userRepository
    ) {
    }
    private $counter = 1;
    // private $counter = 1; //créer un compteur pour pouvoir utiliser user_id
    public function load(ObjectManager $manager): void
    {
        // On ne créé l'utilisateur que si n'existe pas déjà
        if (!$this->userRepository->findOneByEmail('admin@admin.fr')) {
            $admin = new User();

            $admin->setEmail('admin@admin.fr');
            $admin->setUsername('admin');
            $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
            $admin->setRoles(['ROLE_ADMIN']);
            $manager->persist($admin);
        }


        $faker = Factory::create('fr_FR');

        for ($usr = 1; $usr <= 5; $usr++) {
            // On ne créé l'utilisateur que si n'existe pas déjà
            $userEmail = $faker->email;
            if (!$this->userRepository->findOneByEmail($userEmail)) {
                $user = new User();
                $user->setEmail($userEmail);
                $user->setUsername($faker->userName);
                $user->setPassword($this->passwordEncoder->hashPassword($user, 'user'));
                $user->setRoles(['ROLE_USER']);

                $manager->persist($user);
                $this->addReference('user-'.$this->counter, $user);
                $this->counter++; 
            }
        }

        $manager->flush();
    }

    // fonction qui évite redondance pour créer un user 
    public function createUser(string $username, ObjectManager $manager)
    {
        $username = new User();
        $username->setUsername($username);
        $manager->persist($username);

        $this->addReference('' . $this->counter, $username);
        $this->counter++;               //stock des ref que je vais rechercher sur TrickFixtures

        return $username;
    }
}
