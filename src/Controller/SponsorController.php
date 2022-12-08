<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Repository\SponsorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/sponsor')]
class SponsorController extends AbstractController
{
    #[Route('/', name: 'app_sponsor_index', methods: ['GET'])]
    public function index(SponsorRepository $sponsorRepository): Response
    {
        return $this->render('sponsor/index.html.twig', [
            'sponsors' => $sponsorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sponsor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SponsorRepository $sponsorRepository , ValidatorInterface $validator): Response
    {
        $sponsor = new Sponsor();
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $errors = $validator->validate($sponsor);
            // dd($errors);
            if (count($errors) > 0) {
                // dd(1);
                $errorsString = (string) $errors;
                // dd($errors);
                return $this->renderForm('sponsor/new.html.twig', [
                    'sponsor' => $sponsor,
                    'form' => $form,
                    'errors' => $errors
                ]);
            }
            $sponsorRepository->save($sponsor, true);
            $this->addFlash('success_message', 'Sponsor ajouté avec succés');
            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sponsor/new.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
            "errors" => null
        ]);
    }

    #[Route('/{id}', name: 'app_sponsor_show', methods: ['GET'])]
    public function show(Sponsor $sponsor): Response
    {
        return $this->render('sponsor/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsor $sponsor, SponsorRepository $sponsorRepository , ValidatorInterface $validator): Response
    {
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $errors = $validator->validate($sponsor);
            // dd($errors);
            if (count($errors) > 0) {
                // dd(1);
                $errorsString = (string) $errors;
                // dd($errors);
                return $this->renderForm('sponsor/new.html.twig', [
                    'sponsor' => $sponsor,
                    'form' => $form,
                    'errors' => $errors
                ]);
            }
            $sponsorRepository->save($sponsor, true);
            $this->addFlash('success_message', 'Sponsor modifié avec succés');
            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sponsor/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
            'errors' => null
        ]);
    }

    #[Route('/delete/{id}', name: 'app_sponsor_delete')]
    public function delete(Request $request, Sponsor $sponsor, SponsorRepository $sponsorRepository): Response
    {

        $sponsorRepository->remove($sponsor, true);
        $this->addFlash("success_message", "Sponsor supprimé avec succés");

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }
}
