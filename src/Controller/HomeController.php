<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $userRepository, TrickRepository $trickRepository): Response
    {
        // $userRepository->findOneByEmail('admin@admin.fr');
        // dd($userRepository->findOneByEmail('admin@admin.fr'));
        // On récupère les 3 derniers tricks créé
        $tricks = $trickRepository->findBy([], array('created_at' => 'DESC'));
        // $trick = $trickRepository->findBy($id);    
        // $illustrations = $trick->getIllustrations();

        // $illustration = null;
        // if($illustrations && count($illustrations) > 0){
        //     $illustration = $illustrations[0];
        // }
        // $illustrations = $tricks->getIllustrations();

        return $this->render('home/index.html.twig', ['tricks' => $tricks]);
    }


    #[Route('/load-more-tricks', name: 'app_load')]
    public function loadMoreTricks(TrickRepository $trickRepository, Request $request): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $tricks = $trickRepository->findBy([], array('created_at' => 'DESC'), 8, $offset);
        // return $this->render('home/index.html.twig', ['tricks' => $tricks]);
         // Renvoie les données sous forme de JSON
         $response = new JsonResponse();
         $response->setData(['tricks' => $tricks]);
 
         return $response;
    }
}
