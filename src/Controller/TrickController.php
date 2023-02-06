<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick', name: 'app_trick')]
    #[IsGranted('ROLE_USER')]

    // function to display trick page 
    public function index(): Response
    {
        return $this->render('trick/index.html.twig');

    }
}
