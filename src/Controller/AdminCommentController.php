<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="app_admin_comments")
     */
    public function index(CommentRepository $repo): Response
    {

        $comments = $repo->findAll();
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * Permet l'édition d'un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="app_admin_comment_edit")
     * 
     * @return Response
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $em->persist($comment);
           $em->flush();

           $this->addFlash(
               'success',
               "Le commentaire numéros <strong>{$comment->getId()}</strong> à bien été modifier"
           );
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet la suppression d'un commentaire
     *
     * @Route("/admin/comments/{id}/delete", name="app_admin_comment_delete")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $em
     * @return response
     */
    public function delete(Comment $comment, EntityManagerInterface $em)
    {
        $em->remove($comment);
        $em->flush();

        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> à bien été supprimer"
        );

        return $this->redirectToRoute('app_admin_comments');

    }
}
