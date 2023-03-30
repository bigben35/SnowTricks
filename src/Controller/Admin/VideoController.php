<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/video')]
class VideoController extends AbstractController
{
    #[Route('/', name: 'app_admin_video_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('admin/video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VideoRepository $videoRepository): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoRepository->save($video, true);

            return $this->redirectToRoute('app_admin_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/video/new.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_video_show', methods: ['GET'])]
    public function show(Video $video): Response
    {
        return $this->render('admin/video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoRepository->save($video, true);

            return $this->redirectToRoute('app_admin_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_video_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        // Récupération du Token Csrf généré dans la vue HTML (en GET ou POST)
        $tokenCSRF = $request->get('_token') ?? $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $tokenCSRF)) {
            $videoRepository->remove($video, true);
        }

        $this->addFlash('success', 'La video a bien été supprimée');

        if ($request->getMethod() == 'GET') {
            // Redirection vers la page précédente
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->redirectToRoute('app_admin_video_index', [], Response::HTTP_SEE_OTHER);
    }
}
