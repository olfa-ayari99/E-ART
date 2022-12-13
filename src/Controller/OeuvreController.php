<?php

namespace App\Controller;

use App\Entity\Evaluation;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Oeuvre;
use App\Entity\Wishlist;
use App\Form\Oeuvre1Type;
use App\Repository\EvaluationRepository;
use App\Repository\OeuvreRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/oeuvre')]
class OeuvreController extends AbstractController
{


    #[Route('/', name: 'app_oeuvre_index', methods: ['GET'])]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('oeuvre/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }

    #[Route('/wishlist', name: 'app_oeuvre_wishlist_index')]
    public function wishlist(OeuvreRepository $oeuvreRepository , WishlistRepository $wishlistRepository ): Response
    {
        $oeuvres = [];

        foreach ($oeuvreRepository->findAll() as $value) {
           if($value->getWishlist()){
                $oeuvres[] = ["item" => $value];
           }
        }
        // dd($oeuvres);
        return $this->render('oeuvre/wishlist.html.twig', [
            'oeuvres' => $oeuvres,
        ]);
    }

    #[Route('/wishlist/add/{id}', name: 'app_oeuvre_wishlist_add')]
    public function wishlistAdd(Oeuvre $oeuvre, Request $request, OeuvreRepository $oeuvreRepository, EntityManagerInterface $em , WishlistRepository $wishlistRepository): Response
    {
        $wishlist = new Wishlist ;
        $wishlist->addOeuvre($oeuvre);
        $em->persist($wishlist);
        $em->flush();
        return $this->redirectToRoute('app_oeuvre_wishlist_index');
    }

    #[Route('/wishlist/delete/{id}', name: 'app_oeuvre_wishlist_delete')]
    public function wishlistDelete(Oeuvre $oeuvre, Request $request, OeuvreRepository $oeuvreRepository, EntityManagerInterface $em , WishlistRepository $wishlistRepository): Response
    {
        
        $wishlist =$wishlistRepository->find($oeuvre->getWishlist());
        $oeuvre->setWishlist(null);
        $em->remove($wishlist);
        $em->flush();
        return $this->redirectToRoute('app_oeuvre_wishlist_index');
    }

    #[Route('/note/{id}', name: 'app_oeuvre_note', methods: ['POST'])]
    public function note(Oeuvre $oeuvre, EntityManagerInterface $em, Request $request, EvaluationRepository $evaluationRepository)
    {
        if ($request->isMethod("POST")) {
            if ($request->request->get('note') != 0) {
                $evaluation = $evaluationRepository->findOneBy(['oeuvre' => $oeuvre->getId()]);
                if (!$evaluation) {
                    $evaluation = new Evaluation ;
                    $evaluation->setValeur($request->request->get('note'));
                    $evaluation->setOeuvre($oeuvre);
                    $evaluation->setValeur($request->request->get('note'));
                    $em->persist($evaluation);
                }else{
                    $evaluation->setValeur($request->request->get('note'));
                }
                $em->flush();
                $this->addFlash('success_message', 'Merci pour votre evaluation !');
                return $this->redirect($request->headers->get('referer'));
            } else {
                $this->addFlash('error_message', 'Evaluation à 0  !');
                return $this->redirect($request->headers->get('referer'));
            }
        }
    }

    #[Route('/front', name: 'oeuvre_index_front', methods: ['GET'])]
    public function oeuvreFront(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('oeuvre/index_front.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_oeuvre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OeuvreRepository $oeuvreRepository, SluggerInterface $slugger): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(Oeuvre1Type::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('oeuvres_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $oeuvre->setImage($newFilename);
            }
            $oeuvreRepository->save($oeuvre, true);

            $this->addFlash('success_message', 'Oeuvre ajoutée avec succés');
            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('oeuvre/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvre_show', methods: ['GET'])]
    public function show(Oeuvre $oeuvre): Response
    {
        return $this->render('oeuvre/show.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_oeuvre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Oeuvre $oeuvre, OeuvreRepository $oeuvreRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(Oeuvre1Type::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('oeuvres_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $oeuvre->setImage($newFilename);
            }
            $oeuvreRepository->save($oeuvre, true);
            $this->addFlash('success_message', 'Oeuvre modifiée avec succés');
            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('oeuvre/edit.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_oeuvre_delete')]
    public function delete(Request $request, Oeuvre $oeuvre, OeuvreRepository $oeuvreRepository): Response
    {
        $oeuvreRepository->remove($oeuvre, true);
        $this->addFlash('success_message', 'Oeuvre supprimée avec succés');
        return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
    }
}
