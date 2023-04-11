<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    // function to display tricks on page home 
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([], array('created_at' => 'DESC'));

        return $this->render('home/index.html.twig', ['tricks' => $tricks]);
    }


    #[Route('/load-more-tricks', name: 'app_load')]
    public function loadMoreTricks(TrickRepository $trickRepository, Request $request): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $tricks = $trickRepository->findBy([], array('created_at' => 'DESC'), 8, $offset);
         // Renvoie les données sous forme de JSON
         $response = new JsonResponse();
         $response->setData(['tricks' => $tricks]);
 
         return $response;
    }
}
