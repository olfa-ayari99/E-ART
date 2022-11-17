<?php

namespace App\Controller\Gallerie;

use App\Entity\Gallerie;
use App\Entity\User;
use App\Form\GallerieFormType;
use App\Repository\GallerieRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GallerieController extends AbstractController
{
    #[Route('/gallerie', name: 'app_galleries')]
    public function listGallerie(GallerieRepository  $repository)
    {
        $galleries = $repository->findAll();
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

        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($gallerie);
            $em->flush();
            return $this->redirectToRoute("app_galleries");
        }
        return $this->renderForm("Gallerie/add_gallerie.html.twig", array("gallerieForm" => $form));
    }
}
