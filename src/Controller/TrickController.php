<?php

namespace App\Controller;

use App\Entity\Trick;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trick', name: 'trick_')]
class TrickController extends AbstractController
{
    #[Route('', name: 'index')]

    // function to display trick page 
    public function index(): Response
    {
        return $this->render('trick/index.html.twig');
    }

    #[Route('/{slug}', name: 'show')]
    // function to display trick page 
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', ['trick' => $trick]);
    }
}
