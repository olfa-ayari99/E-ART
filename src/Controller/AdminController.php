<?php

namespace App\Controller;

use App\Repository\GallerieRepository;
use App\Repository\NotificationRepository;
use App\Repository\ReservationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController

{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/listGalleries', name: 'app_admin_list_galleries')]
    public function listGalleries(Request $request, GallerieRepository  $gallerieRepository, NotificationRepository $notificationRepository, PaginatorInterface $paginator): Response
    {
        $galleries = $gallerieRepository->findAll();

        $galleries = $paginator->paginate( $galleries, $request->query->getInt('page', 1), 5);

        $notifs = $notificationRepository->findAll();
        return $this->render('admin/liste_galleries.html.twig', ['listGalleries' => $galleries,'listNotif'=>$notifs]);
    }


    #[Route('/admin/pdf', name: 'app_pdf')]
   public function pdfGenerator(Pdf $knpSnappyPdf, GallerieRepository $gallerieRepository )
    {
        $galleries = $gallerieRepository->findAll();
        $html = $this->renderView('admin/pdf.html.twig', ['listGalleries' => $galleries]);

        $pdfToShow = new PdfResponse($knpSnappyPdf->getOutputFromHtml($html), 'file.pdf');
        //return $pdfToShow;

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }



    #[Route('/admin/listReservations', name: 'app_admin_list_reservations')]
    public function listReservations(Request $request, ReservationRepository  $reservationRepository, PaginatorInterface $paginator): Response
    {
        $reservations = $reservationRepository->findAll();

        $reservations = $paginator->paginate( $reservations, $request->query->getInt('page', 1), 5);


        return $this->render('admin/liste_reservations.html.twig', ['listReservations' => $reservations]);
    }

    #[Route('/deleteReservationAdmin/{id}', name: 'app_delete_reservation_admin')]
    public function deleteGallerieAdmin($id, ReservationRepository  $repository, ManagerRegistry $doctrine)
    {
        $reservation = $repository->find($id);
        $em = $doctrine->getManager(); // $em=$this->getDoctrine()->getManager();
        $em->remove($reservation);
        $em->flush();
        return $this->redirectToRoute("app_admin_list_reservations");
    }

    #[Route('/deleteGallerieAdmin/{id}', name: 'app_delete_gallerie_admin')]
    public function deleteGallerie($id, GallerieRepository  $repository, ManagerRegistry $doctrine)
    {
        $gallerie = $repository->find($id);
        $em = $doctrine->getManager(); // $em=$this->getDoctrine()->getManager();
        $em->remove($gallerie);
        $em->flush();
        return $this->redirectToRoute("app_admin_list_galleries");
    }
}

