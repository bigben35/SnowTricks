<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickType;
use App\Entity\Category;
use App\Entity\Illustration;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/admin/trick')]
class TrickController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'app_admin_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('admin/trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_trick_new', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_USER')]
    public function new(Request $request, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        // dd($trick);
        // $slug = $this->setSlug();
        // $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        if ($this->getUser()) {
            $trick->setUser($this->getUser());
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
                    return $this->redirectToRoute('app_admin_trick_new', [], Response::HTTP_SEE_OTHER);
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

            // On récupère toutes les videos
            $videoUrl1 = $form->get('video_1')->getData();

            $video = new Video();

            // On associe la vidéo à la figure
            $video->setMediaLink($videoUrl1);
            
            $trick->addVideo($video);

            //catégorie 
            $categoryName = $form->get('category')->getData();
            if (!empty($categoryName)) {
                $trick->addCategory($categoryName);
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
    
    public function show(Trick $trick): Response
    {
        return $this->render('admin/trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_trick_edit', methods: ['GET', 'POST'])]
    
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, SluggerInterface $slugger, Filesystem $filesystem): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        // dd($trick->getVideos());
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
                        return $this->redirectToRoute('app_admin_trick_edit', [], Response::HTTP_SEE_OTHER);
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

            // if ()
            // On récupère toutes les videos
            $video1 = new Video();
            $videoUrl1 = $form->get('video_1')->getData();
            $video1->setMediaLink($videoUrl1);
            $trick->addVideo($video1);

            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $trickRepository->save($trick, true);

            //catégorie 
            $categoryName = $form->get('category')->getData();
            if (!empty($categoryName)) {
                $trick->addCategory($categoryName);
            }

            /** @var User $user */
            $user = $this->getUser();
            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $trick->setUser($user);

            $trickRepository->save($trick, true);
            $this->addFlash('success', "Votre Figure a bien été modifiée !");
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
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_admin_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
