<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser('ROLE_ADMIN')) {
        //     return $this->redirectToRoute('app_admin'); //par exemple
        // }
        // else {
        //     return $this->redirectToRoute('app_profil');
        // }
        // if ($form->isSubmitted() && $form->isValid()) {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    // }
}


    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // route qui en donnant mon adresse mail, va me permettre de recevoir un lien de réinitialisation du mot de passe
    #[Route(path: '/oubli-pass', name: 'forgotten_password')]
    public function forgottenPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);     //gere la requete, passer $request en param de finction forgottenPassword (injection de dépendances)

        if ($form->isSubmitted() && $form->isValid()) {
            // on va chercher l'user par son username 
            $user = $userRepository->findOneByUsername($form->get('username')->getData()); //je vais chercher données de mon champ username dans formulaire
            // dd($user);
            //on vérifie si on a un user
            if ($user) {
                //on génère un token de réinitialisation
                $token = $tokenGeneratorInterface->generateToken();  //genère un token, passer $otenGeneratorInterface en param de fonction forgottenPassword (injection de dépendances)
                // dd($token);
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                //on génère un lien de réinitialisation du mdp
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                // dd($url);

                //on crée les données du mail
                $context = compact('url', 'user');           //compact permet de faire un tableau avec url => $url et user => $user

                //envoi du mail
                $mail->send(
                    'no-reply@SnowTricks.com',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès !');
                return $this->redirectToRoute('app_login');
            }
            //si $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [  //on passe le formulaire dans un tableau pour qu'il s'affiche avec twig
            'requestPassForm' => $form->createView()   //tu crées la vue html du form et tu la passes à ma vue sous le nom 'requestPassForm'
        ]);
    }


    #[Route(path: '/oubli-pass/{token}', name: 'reset_pass')]  //route pour new password
    public function resetPass(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        //on vérifie si on a ce token dans la bdd
        $user = $userRepository->findOneByResetToken($token);
        // dd($user);

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //on efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié !');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
