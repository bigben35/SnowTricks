<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]

    
    public function index(UserRepository $userRepository): Response
    {
        // $userRepository->findOneByEmail('admin@admin.fr');
        // dd($userRepository->findOneByEmail('admin@admin.fr'));
        return $this->render('home/index.html.twig');
    }
}
