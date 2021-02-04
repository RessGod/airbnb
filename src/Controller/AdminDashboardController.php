<?php

namespace App\Controller;

use App\Service\StatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin_dashboard")
     */
    public function index(EntityManagerInterface $em, StatService $statsService): Response
    {

        $stats = $statsService->getStats();

        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            // 'users' => $users,
            // 'ads' => $ads,
            // 'bookings' => $bookings,
            // 'comments' => $comments
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
