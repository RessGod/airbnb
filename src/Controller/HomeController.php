<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="app_home")
     */
   public function Home(AdRepository $repo, UserRepository $userRepo):response
   {
       return $this->render(
           'home.html.twig',
           [
               'ads' => $repo->findBestAds(3),
               'users' => $userRepo->findBestUsers()
           ]
        );
   } 

}

?>