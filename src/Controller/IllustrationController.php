<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Illustration;
use App\Form\IllustrationType;
use App\Repository\IllustrationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/illustration')]
class IllustrationController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'illustration_index', methods: ['GET'])]
    public function index(IllustrationRepository $illustrationRepository): Response
    {
        return $this->render('illustration/index.html.twig', [
            'illustrations' => $illustrationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'illustration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IllustrationRepository $illustrationRepository): Response
    {
        $illustration = new Illustration();
        $form = $this->createForm(IllustrationType::class, $illustration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $illustrationRepository->save($illustration, true);

            return $this->redirectToRoute('illustration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('illustration/new.html.twig', [
            'illustration' => $illustration,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'illustration_show', methods: ['GET'])]
    public function show(Illustration $illustration): Response
    {
        return $this->render('illustration/show.html.twig', [
            'illustration' => $illustration,
        ]);
    }

    #[Route('/{id}/edit', name: 'illustration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Illustration $illustration, IllustrationRepository $illustrationRepository, SluggerInterface $slugger): Response
    {
        $trick = $illustration->getTrick();

        $form = $this->createForm(IllustrationType::class, $illustration);
        $form->handleRequest($request);
        // dd($form);

        if ($form->isSubmitted() && $form->isValid()) {
            // $illustration = $form->getData();

            // On récupère image
                $illustrationFile = $form->get('file')->getData();
                // dd($illustrationFile);
            
                if ($illustrationFile) {
                    $originalFilename = pathinfo($illustrationFile->getClientOriginalName(), PATHINFO_FILENAME); 
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $illustrationFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $illustrationFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', $e);
                        return $this->redirectToRoute('illustration_edit', ['id' => $illustration->getId()], Response::HTTP_SEE_OTHER);
                    }

                    //on récupère le trick associé
                    $trick = $illustration->getTrick();

                    //on supprime l'illustration
                    $illustrationRepository->remove($illustration);

                    // Pour chaque fichier à uploader, on créé une nouvelle instance de l'illustration
                    $illustration = new Illustration();

                    // On associe l'illustration à la figure
                    $illustration->setFile($newFilename);
                    
                    $trick->addIllustration($illustration);
                }
                $illustrationRepository->save($illustration, true);
    
                $this->addFlash('success', 'L\'illustration a bien été modifiée');
                return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
            }
        

        return $this->renderForm('illustration/edit.html.twig', [
            'illustration' => $illustration,
            'form' => $form,
            'trick' => $trick,
        ]);
    }
    #[Route('/{id}/delete', name: 'illustration_delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Illustration $illustration, IllustrationRepository $illustrationRepository, Trick $trick): Response
    {
        // Récupération du Token Csrf généré dans la vue HTML (en GET ou POST)
        $tokenCSRF = $request->get('_token') ?? $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $illustration->getId(), $tokenCSRF)) {
            // Si le token est valide (transation valide), on supprime l'illustration
            $illustrationRepository->remove($illustration, true, $this->getParameter('images_directory'));
        }

        $this->addFlash('success', 'L\'illustration a bien été supprimée');

        // Si requete effectuée en GET, Redirection vers la page précédente
        if ($request->getMethod() == 'GET') {
            return $this->redirect($request->headers->get('referer'));
        }

        // // Sinon, on redirige vers la page listant les illustrations
        // return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        // Redirection vers la page de détails du Trick d'où provient l'illustration supprimée
    $trick = $illustration->getTrick();
    return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
