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
use Symfony\Component\String\Slugger\SluggerInterface;

class GallerieController extends AbstractController
{
    #[Route('/gallerie', name: 'app_galleries')]
    public function listGallerie(GallerieRepository  $repository)
    {
        $galleries = $repository->findAll();
        return $this->render("Gallerie/gallerie.html.twig", array("listGalleries" => $galleries));
    }

    #[Route('/addGallerie', name: 'app_add_gallerie')]
    public function addGallerie(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository, SluggerInterface $slugger)
    {
        $gallerie = new Gallerie();
        $currentUser = $userRepository->find(1);

        $gallerie->setEtat(0);
        $gallerie->setUser($currentUser);

        $form = $this->createForm(GallerieFormType::class, $gallerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

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
            $em->persist($gallerie);
            $em->flush();
            return $this->redirectToRoute("app_galleries");
        }
        return $this->renderForm("Gallerie/add_gallerie.html.twig", array("gallerieForm" => $form));
    }


}
