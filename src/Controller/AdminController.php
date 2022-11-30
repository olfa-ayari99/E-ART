<?php

namespace App\Controller;

use App\Repository\GallerieRepository;
use App\Repository\NotificationRepository;
use Knp\Component\Pager\PaginatorInterface;
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
}
