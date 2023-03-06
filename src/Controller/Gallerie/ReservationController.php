<?php

namespace App\Controller\Gallerie;

use App\Repository\ReservationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/listReservation', name: 'app_list_reservation')]
    public function listReservation(ReservationRepository $repository, PaginatorInterface $paginator, Request $request,): Response
    {
        $reservations = $repository->findAll();

        $reservations = $paginator->paginate($reservations, $request->query->getInt('page', 1), 5);
        return $this->render("Gallerie/reservation.html.twig", array("listReservations" => $reservations));
    }

    #[Route('/deleteReservation/{id}', name: 'app_delete_reservation')]
    public function deleteGallerie($id, ReservationRepository  $repository, ManagerRegistry $doctrine)
    {
        $reservation = $repository->find($id);
        $em = $doctrine->getManager(); // $em=$this->getDoctrine()->getManager();
        $em->remove($reservation);
        $em->flush();
        return $this->redirectToRoute("app_list_reservation");
    }



}