<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="app_account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        // Pour préchargez le nom d'utilisateur
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error != null,
            'username' => $username
        ]);
    }

    /**
     * @Route("/logout", name="app_account_logout")
     */
    public function logout(): Response
    {
        // return $this->render('account/login.html.twig');
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="app_account_register")
     */
    public function register(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créer! Vous pouvez maintenant vous connecter !"
            );

            return $this->redirectToRoute('app_account_login');
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]); 
    }

    /**
     * Permet la modification du profil utilisateur
     *
     * @Route("/account/profile", name="app_account_profile")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profile(EntityManagerInterface $em, Request $request)
    {

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFirstName()}</strong> a bien été modifiée !"
            );

            // return $this->redirectToRoute('app_account_show', [
            //     'slug' => $ad->getSlug()
            // ]);
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/update-password", name="app_account_password")
     *
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();
        
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1 - Vérifier que le oldPassword du formulaire soit le même que le password de l'user 
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                // Récupération et hashage du nouveau mot de passe
                $hash = $encoder->encodePassword($user, $passwordUpdate->getNewPassword());
                $user->setHash($hash);


                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    "Le mot de passe a bien été modifiée !"
                );

                return $this->redirectToRoute('app_home');

            }
           
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecter
     *
     * @Route("/account", name="app_account_index")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * Permet d'afficher la liste des réservations faites par l'utilisateur
     *
     * @Route("/account/bookings", name="app_account_bookings")
     * 
     * @return Response
     */
    public function bookings()
    {
        return $this->render('account/bookings.html.twig');
    }
}
