<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $userRepository, TrickRepository $trickRepository): Response
    {
        // $userRepository->findOneByEmail('admin@admin.fr');
        // dd($userRepository->findOneByEmail('admin@admin.fr'));
        // On récupère les 3 derniers tricks créé
        $tricks = $trickRepository->findBy([], null, 3);

        return $this->render('home/index.html.twig', ['tricks' => $tricks]);
    }
}
