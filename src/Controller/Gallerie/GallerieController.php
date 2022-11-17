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
}
