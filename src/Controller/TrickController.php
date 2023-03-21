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
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentTrickRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/trick', name: 'trick_')]
class TrickController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    // #[Route('', name: 'index')]

    // // function to display trick page 
    // public function index(): Response
    // {
    //     return $this->render('trick/index.html.twig');
    // }

    #[Route('/{slug}', name: 'show')]
    // function to display trick page 
    public function show(Trick $trick, Request $request, CommentTrickRepository $commentTrickRepository, EntityManagerInterface $entityManager): Response
    {

        if(!$trick) {
            return $this->redirectToRoute('app_home');
        }

        // commentaires 
        $commentTrick = new CommentTrick();
        $form = $this->createForm(CommentType::class, $commentTrick);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // dd($commentTrick);
            $connectedUser = $this->getUser();
            $commentTrick->setConnectedUser($connectedUser);
            $commentTrick->setTrick($trick);
            $commentTrickRepository->save($commentTrick, true);
            $this->addFlash('success', "Votre Commentaire a bien été créé !");
            
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);

        }

        
        // Pagination
        $page = $request->query->getInt('page', 1);
        $limit = 3;
        $offset = ($page - 1) * $limit;

        $query = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(CommentTrick::class, 'c')
            ->where('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $commentTricks = $paginator->getIterator();
        
        //on va chercher commentaires d'un trick
        // $commentTricks = $commentTrickRepository->findBy(['trick' => $trick],
        // ['createdAt' => 'DESC'],
        // $limit,
        // $offset);

        // $totalCommentaires = count($commentTricks);
        // $totalPages = ceil($totalCommentaires / $limit);
        
        // $paginatedComments = array_slice($commentTricks, $offset, $limit);
        // $totalComments = count($commentTricks);
        // $page = $request->query->getInt('page', 1); // Si le paramètre page n'est pas présent, on utilise la valeur 1 par défaut
        // $limit = 3; // nombre de commentaires par page
        // $offset = ($page - 1) * $limit;

        // $pagedComments = array_slice($commentTricks, $offset, $limit);
        // $totalItems = count($commentTricks); // Nombre total d'éléments

        // // dd($offset);
        // $commentTricks = $commentTrickRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
        
        // // dd($commentTricks);
        // // $queryBuilder = $commentTrickRepository->createQueryBuilder('c');
        // // $queryBuilder->setFirstResult($offset);
        // // $queryBuilder->setMaxResults($limit);
        // // $queryBuilder->orderBy('c.createdAt', 'DESC');

        // // $commentTricks = $queryBuilder->getQuery()->getResult();


        // // calculer le nombre total de pages
        // $totalPages = ceil(count($commentTricks) / $limit);


        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'commentTricks' => $commentTricks,
            'page' => $page,
            'nb_pages' => ceil(count($paginator) / $limit),
            // 'currentPage' => $page,
            // 'totalPages' => $totalPages
        ]);
    }



    
    // edit 
    
    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
        
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
                        return $this->redirectToRoute('app_trick_edit', [], Response::HTTP_SEE_OTHER);
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
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }
}



