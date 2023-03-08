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
        $tricks = $trickRepository->findBy([], array('created_at' => 'DESC'), 8);
        // $trick = $trickRepository->findBy($id);    
        // $illustrations = $trick->getIllustrations();

        // $illustration = null;
        // if($illustrations && count($illustrations) > 0){
        //     $illustration = $illustrations[0];
        // }
        // $illustrations = $tricks->getIllustrations();

        return $this->render('home/index.html.twig', ['tricks' => $tricks]);
    }
}
