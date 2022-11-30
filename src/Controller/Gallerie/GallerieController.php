<?php

namespace App\Controller\Gallerie;

use App\Entity\Gallerie;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\GallerieFormType;
use App\Repository\GallerieRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class GallerieController extends AbstractController
{
    #[Route('/gallerie', name: 'app_galleries')]
    public function listGallerie(GallerieRepository $repository, PaginatorInterface $paginator, Request $request,): Response
    {
        $galleries = $repository->findAll();

        $galleries = $paginator->paginate($galleries, $request->query->getInt('page', 1), 5);
        return $this->render("Gallerie/gallerie.html.twig", array("listGalleries" => $galleries));
    }

    #[Route('/addGallerie', name: 'app_add_gallerie')]
    public function addGallerie(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository)
    {
        $gallerie = new Gallerie(); //création d'une instance gallerie pour l'ajouter dans la bdd.
        $currentUser = $userRepository->find(1); //récupération de propriétaire d'id = 1. Statique pour le moment.

        $gallerie->setEtat(0); // état = 0 : disponible pour la réserver, 1 sinon.
        $gallerie->setUser($currentUser); //attribution de cla étrangère dans la table gallerie.

        $form = $this->createForm(GallerieFormType::class, $gallerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($gallerie);
            $em->flush();

            $notif = new Notification();
            $notif->setType("Ajout");
            $notif->setIdGallerie($gallerie->getId());
            $notif->setDate(new \DateTime('now'));
            $notif->setMessage("Nouvelle gallerie ajoutée");

            $em = $doctrine->getManager();
            $em->persist($notif);
            $em->flush();
            return $this->redirectToRoute("app_galleries");
        }
        return $this->renderForm("Gallerie/add_gallerie.html.twig", array("gallerieForm" => $form));
    }



    #[Route('/detailGallerie/{id}', name: 'app_detail_gallerie')]
    public function detailGalleries($id, GallerieRepository  $repository)
    {
        $gallerie = $repository->find($id);
        return $this->render("Gallerie/detail_gallerie.html.twig", array("gallerie" => $gallerie));
    }

    #[Route('/myGalleries', name: 'app_my_galleries')]
    public function myGalleries(GallerieRepository  $repository)
    {
        $galleries = $repository->findAll();
        return $this->render("Gallerie/my_galleries.html.twig", array("listGalleries" => $galleries));
    }

    #[Route('/deleteGallerie/{id}', name: 'app_delete_gallerie')]
    public function deleteGallerie($id, GallerieRepository  $repository, ManagerRegistry $doctrine)
    {
        $gallerie = $repository->find($id);
        $em = $doctrine->getManager(); // $em=$this->getDoctrine()->getManager();
        $em->remove($gallerie);
        $em->flush();

        $notif = new Notification();
        $notif->setType("Suppression");
        $notif->setDate(new \DateTime('now'));
        $notif->setIdGallerie($id);
        $notif->setMessage("Gallerie supprimée");

        $em = $doctrine->getManager();
        $em->persist($notif);
        $em->flush();

        return $this->redirectToRoute("app_my_galleries");
    }


    #[Route('/editGallerie/{id}', name: 'app_edit_gallerie')]
    public function editGallerie($id, GallerieRepository  $repository, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger)
    {
        $gallerie = $repository->find($id);
        $form = $this->createForm(GallerieFormType::class, $gallerie,);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('galleries_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $gallerie->setImage($newFilename);
            }

            $em = $doctrine->getManager();
            $em->flush();

            $notif = new Notification();
            $notif->setType("Modification");
            $notif->setDate(new \DateTime('now'));
            $notif->setIdGallerie($id);
            $notif->setMessage("Gallerie modifiée");

            $em = $doctrine->getManager();
            $em->persist($notif);
            $em->flush();
            return $this->redirectToRoute("app_my_galleries");
        }
        return $this->renderForm("Gallerie/edit_gallerie.html.twig", array("gallerieForm" => $form, "gallerie" => $gallerie));
    }

    #[Route('/test', name: 'app_test')]
    public function reservation(ReservationRepository $reservationRepository)
    {

        $current = $reservationRepository->find(1);
        return $this->render("Gallerie/reservation.html.twig", array("current" => $current));
    }
}
