<?php

namespace App\Controller\Admin;

use App\Entity\Illustration;
use App\Form\IllustrationType;
use App\Repository\IllustrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/illustration')]
class IllustrationController extends AbstractController
{
    #[Route('/', name: 'app_admin_illustration_index', methods: ['GET'])]
    public function index(IllustrationRepository $illustrationRepository): Response
    {
        return $this->render('admin/illustration/index.html.twig', [
            'illustrations' => $illustrationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_illustration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IllustrationRepository $illustrationRepository): Response
    {
        $illustration = new Illustration();
        $form = $this->createForm(IllustrationType::class, $illustration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $illustrationRepository->save($illustration, true);

            return $this->redirectToRoute('app_admin_illustration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/illustration/new.html.twig', [
            'illustration' => $illustration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_illustration_show', methods: ['GET'])]
    public function show(Illustration $illustration): Response
    {
        return $this->render('admin/illustration/show.html.twig', [
            'illustration' => $illustration,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_illustration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Illustration $illustration, IllustrationRepository $illustrationRepository): Response
    {
        $form = $this->createForm(IllustrationType::class, $illustration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $illustrationRepository->save($illustration, true);

            return $this->redirectToRoute('app_admin_illustration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/illustration/edit.html.twig', [
            'illustration' => $illustration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_illustration_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Illustration $illustration, IllustrationRepository $illustrationRepository): Response
    {
        // Récupération du Token Csrf généré dans la vue HTML (en GET ou POST)
        $tokenCSRF = $request->get('_token') ?? $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $illustration->getId(), $tokenCSRF)) {
            // Si le token est valide (transation valide), on supprime l'illustration
            $illustrationRepository->remove($illustration, true, $this->getParameter('images_directory'));
        }


        // Si requete effectuée en GET, on redirige vers la page précédente (referer)
        if ($request->getMethod() === 'GET') {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }

        // Sinon, on redirige vers la page listant les illustration
        return $this->redirectToRoute('app_admin_illustration_index', [], Response::HTTP_SEE_OTHER);
    }
}
