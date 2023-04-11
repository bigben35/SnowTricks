<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickType;
use App\Entity\Category;
use App\Form\CommentType;
use App\Entity\CommentTrick;
use App\Entity\Illustration;
use App\Repository\TrickRepository;
use App\Repository\CommentTrickRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

#[Route('/trick', name: 'trick_')]
class TrickController extends AbstractController
{
    // function to display trick page 
    #[Route('', name: 'index')]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'tricks' => $trickRepository->findBy([], array('created_at' => 'DESC'))
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]

    // function to create a new Trick 
    public function new(Request $request, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        if ($this->getUser()) {
            $trick->setUser($this->getUser());
        }

        //avoir un champ vidéo vide au départ
        $trick->addEmptyVideo();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère toutes les images (multiple à true ==> Tableau d'images)
            $illustrationFiles = $form->get('files')->getData();

            // Pour chaque image, on créé une illustration que l'on associe à l'entité Trick
            foreach ($illustrationFiles as $illustrationFile) {
                if ($illustrationFile) {
                    // TODO : déplacer cette logique métier dans un Service

                    $originalFilename = pathinfo($illustrationFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $illustrationFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $illustrationFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $this->addFlash('error', $e);
                        return $this->redirectToRoute('trick_new', [], Response::HTTP_SEE_OTHER);
                    }

                    // Pour chaque fichier à uploader, on créé une nouvelle instance de l'illustration
                    $illustration = new Illustration();


                    // On associe l'illustration à la figure
                    $illustration->setFile($newFilename);

                    // Pour la persistence automatique de la nouvelle illustration, 
                    // j'ai mis à jours l'entité Trick (src/Entity/Trick.php à la ligne 54 : cascade: ['persist'])
                    // ce "cascade: ['persist']" permet de ne pas devoir faire de $entityManager->persist($illustration)
                    $trick->addIllustration($illustration);
                }
            }

            /** @var User $user */
            $user = $this->getUser();
            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $trick->setUser($user);
            $trickRepository->save($trick, true);
            $this->addFlash('success', "Votre Figure a bien été créée !");

            return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'form' => $form,
            'trick' => $trick,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    // function to display trick page 
    public function show(Trick $trick, Request $request, CommentTrickRepository $commentTrickRepository): Response
    {

        if (!$trick) {
            return $this->redirectToRoute('app_home');
        }

        // commentaires 
        $commentTrick = new CommentTrick();
        $form = $this->createForm(CommentType::class, $commentTrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $connectedUser = $this->getUser();
            $commentTrick->setConnectedUser($connectedUser);
            $commentTrick->setTrick($trick);
            $commentTrickRepository->save($commentTrick, true);
            $this->addFlash('success', "Votre Commentaire a bien été créé !");

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }


        // Pagination
        //on va chercher n° page dans url
        $page = $request->query->getInt('page', 1);


        // on va chercher les commentaires 
        $commentTricks = $commentTrickRepository->findCommentsPaginated($page, $trick->getSlug(), 2);

        //nombre illustrations par Trick
        $illustrations = $trick->getIllustrations();
        $count = count($illustrations);

        //nombre videos par Trick
        $videos = $trick->getVideos();
        $countVideo = count($videos);


        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'commentTricks' => $commentTricks,
            'count' => $count,
            'countVideo' => $countVideo,
        ]);
    }




    // edit 

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $trick->getUser() != $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'éditer cette figure !");
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère toutes les images (multiple à true ==> Tableau d'images)
            $illustrationFiles = $form->get('files')->getData();

            // Pour chaque image, on créé une illustration que l'on associe à l'entité Trick
            foreach ($illustrationFiles as $illustrationFile) {
                if ($illustrationFile) {
                    // TODO : déplacer cette logique métier dans un Service

                    $originalFilename = pathinfo($illustrationFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $illustrationFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $illustrationFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $this->addFlash('error', $e);
                        return $this->redirectToRoute('trick_edit', [], Response::HTTP_SEE_OTHER);
                    }

                    // Pour chaque fichier à uploader, on créé une nouvelle instance de l'illustration
                    $illustration = new Illustration();

                    // On associe l'illustration à la figure
                    $illustration->setFile($newFilename);
                    $illustration->setTrick($trick);

                    // Pour la persistence automatique de la nouvelle illustration, 
                    // j'ai mis à jour l'entité Trick (src/Entity/Trick.php à la ligne 54 : cascade: ['persist'])
                    // ce "cascade: ['persist']" permet de ne pas devoir faire de $entityManager->persist($illustration)
                    $trick->addIllustration($illustration);
                }
            }


            /** @var User $user */

            $trickRepository->save($trick, true);
            $this->addFlash('success', "Votre Figure a bien été modifiée !");
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $trick->getUser() != $this->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas l'autorisation pour supprimer ce Trick !");
        }

        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
