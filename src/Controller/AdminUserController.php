<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="app_admin_user")
     */
    public function index(UserRepository $repo, $page, PaginationService $pagination): Response
    {

        $pagination->setEntityClass(User::class)
                    ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

     /**
     * Permet l'édition d'un utilisateur
     *
     * @Route("/admin/users/{id}/edit", name="app_admin_user_edit")
     * 
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $em->persist($user);
           $em->flush();

           $this->addFlash(
               'success',
               "L'utilisateur <strong>{$user->getFullName()}</strong> à bien été modifier"
           );

           return $this->redirectToRoute('app_admin_user');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet la suppression d'un commentaire
     *
     * @Route("/admin/users/{id}/delete", name="app_admin_user_delete")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $em
     * @return response
     */
    public function delete(User $user, EntityManagerInterface $em)
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            "L'utilisateur de <strong>{$user->getFullName()}</strong> à bien été supprimer"
        );

        return $this->redirectToRoute('app_admin_user');

    }
}
