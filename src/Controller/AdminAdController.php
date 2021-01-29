<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="app_admin_ads")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination): Response
    {
        // $limit = 10;

        // // Calcul de le nombre des annonces à afficher
        // $start = $page * $limit - $limit;
        // // 1 = 1 * 10 = 10 - 10 = 0 => start
        // // 2 = 2 * 10 = 20 - 10 = 10 => start

        // // Pour avoir le nombre total de mes annonces
        // $total = count($repo->findAll());

        // // Pour arrondir a l'entier supérieur au cas ou on a des nombres a virgul tel que 3.4 = 4
        // $pages = ceil($total / $limit);

        $pagination->setEntityClass(Ad::class)
                    ->setPage($page);      

        return $this->render('admin/ad/index.html.twig', [
            // 'ads' => $repo->findBy([], [], $limit, $start),
            // 'pages' => $pages,
            // 'page' => $page
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'éditer une annonce en utilisant AdType
     *
     * @Route("/admin/ads/{id}/edit", name="app_admin_ads_edit")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> à bien été enregistrée"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="app_admin_ads_delete")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $em)
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède
                déjà des réservations !"
            );
        } else {
            $em->remove($ad);
            $em->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> à bien été supprimée !"
            );
        }
        
        return $this->redirectToRoute('app_admin_ads');
    }
}
