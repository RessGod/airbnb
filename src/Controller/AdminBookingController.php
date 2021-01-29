<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="app_admin_booking")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Booking::class)
                    ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet la modification d'une réservation
     *
     * @Route("/admin/bookings/{id}/edit", name="app_admin_booking_edit")
     * 
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $em):response
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $em->persist($booking);
            $em->flush();

            $this->addFlash(
                'success',
                "La réservation n°{$booking->getId()} à bien été modifier"
            );

            return $this->redirectToRoute('app_admin_booking');
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * Suppression
     * 
     * @Route("/admin/bookings/{id}/delete", name="app_admin_booking_delete")
     * 
     * @param Booking $booking
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $em): Response
    {
        $em->remove($booking);
        $em->flush();

        $this->addFlash(
            'success',
            "La réservation à bien été supprimer"
        );

        return $this->redirectToRoute('app_admin_booking');
    }
}
