<?php

namespace App\DataFixtures;

use App\Entity\CommentTrick;
use App\Entity\Illustration;
use Faker;
use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger, private UserRepository $userRepository)
    {
    }

    public function getDependencies()
    {
        // Par défaut, Symfony execute les fixtures par ordre alphabétiques des noms de fichiers
        // getDependencies Permet au UserFixtures d'etre executé avant le TrickFixture 
        // (car on a besoin d'un user pour créer des Trick)
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generatot instance
        $faker = Faker\Factory::create('fr_FR');

        $trickList = array(
            'Backside 180',
            'Backside 360',
            'Backside Caballerial',
            'Backside Half Cab',
            'Fakie Ollie',
            'Frontside 180',
            'Frontside 360',
            'Frontside Caballerial',
            'Frontside Half Cab',
            'Kickturn'
        );

        for ($index = 0; $index < 10; $index++) {
            $trick = new Trick();

            $trickName = $trickList[$index];
            $trick->setName($trickName);
            // $trick->setName($faker->text(5) . ' ' . $faker->lexify());
            // trick->setAuthor($faker->text(5));
            // $user = $this->getReference(1, 6);
            // trick->setAuthor($faker->name());

            $trick->setDescription($faker->text());
            $trick->setSlug($this->slugger->slug($trickName)->lower());

            // Association d'un utilisateur
            $user = $this->userRepository->findOneByEmail('admin@admin.fr');
            $trick->setUser($user);

            // Association avec catégorie
            $category = $this->getReference('category-' . rand(1, 5));
            $trick->addCategory($category);

            // Ajout de vidéos
            $video1 = (new Video())->setMediaLink('https://www.youtube.com/watch?v=mBB7CznvSPQ');
            $video2 = (new Video())->setMediaLink('https://www.youtube.com/watch?v=AMH992l_nRg');
            $trick->addVideo($video1);
            $trick->addVideo($video2);


            // Ajout d'illustrations
            $illustration = new Illustration();
            $illustration->setFile('trick-default/trick-example-1.jpg');
            $trick->addIllustration($illustration);

            // Ajout d'un commentaire
            $comment = new CommentTrick();
            $comment->setConnectedUser($user);
            $comment->setComment($faker->realText());
            $trick->addCommentTrick($comment);


            $manager->persist($trick);
        }

        $manager->flush();
    }
}
