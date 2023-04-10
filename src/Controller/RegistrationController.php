<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserRepository $userRepository, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            //avatar
            $avatarFile = $form->get('avatar')->getData();
        
            if ($avatarFile instanceof UploadedFile) {
                if ($avatarFile->isValid()) {
                    $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $avatarFileName = uniqid().'.'.$avatarFile->guessExtension();

                    try {
                        $avatarFile->move(
                            $this->getParameter('avatars_directory'),
                            $avatarFileName
                        );
                    } catch (FileException $e) {
                        // Handle the exception
                        $this->addFlash('error', $e);
                    }

                    $user->setAvatar($avatarFileName);
                }
            } 
        

            
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);

            $userRepository->save($user, true);
            // do anything else you need here, like send an email

            // on génère le JWT de l'utilisateur 
            // on crée le header 
            $header = [                                     // voir le site jwt.io pour les infos
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // on crée le payload 
            $payload = [
                'user_id' => $user->getId()
            ];

            // on génère le token 
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


            // on envoie un mail 
            $mail->send(
                'no-reply@monsite.fr',
                $user->getEmail(),
                'Activation de votre compte sur le site SnowTricks',
                'register',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );


            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            $this->addFlash('success', "Votre Compte a bien été créé ! Vérifiez votre boîte mail pour valider votre compte");
            return $this->redirectToRoute('app_profil');
        }
        

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // dd($jwt->getPayload($token));
        // dd($jwt->isExpired($token));
        // dd($jwt->check($token, $this->getParameter('app.jwtsecret')));

        // on vérifie si le token est valide, n'a pas expiré et n'a pas érté modifié
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            // on récupère le payload 
            $payload = $jwt->getPayload($token);

            // on récupère le user du token 
            $user = $userRepository->find($payload['user_id']);

            // on vérifie que l'user existe et n'a pas encore activé son compte 
            if($user && !$user->getIsVerified())
            {
                $user->setIsVerified(true);
                $entityManager->flush($user);
                $this->addFlash('success', 'Utilisateur activé !');
                return $this->redirectToRoute('app_login');  // redirection à modifier lorsque que je vais créer un compte user
            }

        }
        // ici un pb se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré !');
        return $this->redirectToRoute('app_login');
    }


    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail): Response
    {
        $user = $this->getUser();  //récupère l'user connecté

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page !');
            return $this->redirectToRoute('app_login');
        }

        if($user->getIsVerified()){
            $this->addFlash('warning', 'Cet utilisateur est déjà activé !');
            return $this->redirectToRoute('app_login');  // à modifier pour rediriger sur compte user
        }

        // on génère le JWT de l'utilisateur 
            // on crée le header 
            $header = [                                     // voir le site jwt.io pour les infos
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // on crée le payload 
            $payload = [
                'user_id' => $user->getId()
            ];

            // on génère le token 
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


            // on envoie un mail 
            $mail->send(
                'no-reply@monsite.fr',
                $user->getEmail(),
                'Activation de votre compte sur le site SnowTricks',
                'register',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );
            $this->addFlash('success', 'Email de vérification envoyé !');
            return $this->redirectToRoute('app_login'); // à modifier sur compte user
    }
}
