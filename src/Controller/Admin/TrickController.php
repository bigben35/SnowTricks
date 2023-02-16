<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/trick')]
class TrickController extends AbstractController
{
    #[Route('/', name: 'app_admin_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('admin/trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #[Route('/new', name: 'app_admin_trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        // dd($trick);
        // $slug = $this->setSlug();
            // $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        if($this->getUser()){
            $trick->setUser($this->getUser());
        }
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 

            $illustrationFile = $form->get('filename')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($illustrationFile) {
                $originalFilename = pathinfo($illustrationFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$illustrationFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $illustrationFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $trick->setIllustration($newFilename);
            }
            /** @var User $user */
            $user = $this->getUser();
            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $trick->setUser($user);
            $trickRepository->save($trick, true);
            $this->addFlash('success', "Votre Figure a bien été créée !");

            return $this->redirectToRoute('app_admin_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/trick/new.html.twig', [
            'form' => $form,
            'trick' => $trick,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_trick_show', methods: ['GET'])]
    // #[IsGranted('ROLE_USER')]
    public function show(Trick $trick): Response
    {
        return $this->render('admin/trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_trick_edit', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->save($trick, true);

            return $this->redirectToRoute('app_admin_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_admin_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
