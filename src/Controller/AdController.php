<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="app_ads")
     */
    public function index(AdRepository $repo): Response
    {
        // $repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet d'afficher une seule annonce avec l'utilisation du paramConverter 
     * 
     * @Route("/ads/{slug}", name="app_ads_show")
     */
    public function show(Ad $ad): Response
    {
        // $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }
    // Notre foction avant l'utilisation du paramConverter
    // public function show($slug, AdRepository $repo): Response
    // {
    //     $ad = $repo->findOneBySlug($slug);

    //     return $this->render('ad/show.html.twig', [
    //         'ad' => $ad
    //     ]);
    // }
}
