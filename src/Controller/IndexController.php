<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        // dd($evenementRepository->findBy( [] , [] , 3)[0]->getSponsor());
        return $this->render('index/index.html.twig', [
            'events' => $evenementRepository->findBy( [] , [] , 3),
        ]);
    }
}
