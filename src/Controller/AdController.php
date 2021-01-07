<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Permet de créer une annonce
     * @Route("/ads/create",name="app_ads_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $ad = new Ad;


        /** 
         * Ancienne m"thode sans avoir a utiliser AdType
        */ 
        // $form = $this->createFormBuilder($ad)
        //                 ->add('title')
        //                 ->add('introduction')
        //                 ->add('content')
        //                 ->add('rooms')
        //                 ->add('price')
        //                 ->add('coverImage')
        //                 ->add('save', SubmitType::class, [
        //                     'label' => 'Créer la nouvelle annonce',
        //                     'attr' => [
        //                         'class' => 'btn btn-primary'
        //                     ]
        //                 ])
        //                 ->getForm();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Pour persister les images
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $em->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $em->persist($ad);
            $em->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('app_ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

       return $this->render('ad/new.html.twig', [
           'form' => $form->createView()
       ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="app_ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * @return respose
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Pour persister les images
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $em->persist($image);
            }

            $em->persist($ad);
            $em->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée !"
            );

            return $this->redirectToRoute('app_ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView()
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
    // Notre fonction avant l'utilisation du paramConverter
    // public function show($slug, AdRepository $repo): Response
    // {
    //     $ad = $repo->findOneBySlug($slug);

    //     return $this->render('ad/show.html.twig', [
    //         'ad' => $ad
    //     ]);
    // }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/ads/{slug}/delete", name="app_ads_delete")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous n'avez pas le droit d'accéder à cette ressource")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $em)
    {
        $em->remove($ad);
        $em->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("app_ads");
    }

    
}
