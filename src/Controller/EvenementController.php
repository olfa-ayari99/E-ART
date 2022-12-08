<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\TypeEvenementRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/front', name: 'app_evenement_front_index', methods: ['GET'])]
    public function indexFront(EvenementRepository $evenementRepository, TypeEvenementRepository $typeEvenementRepository): Response
    {
        return $this->render('evenement/index_front.html.twig', [
            'evenements' => $evenementRepository->findAll(),
            'typesE' => $typeEvenementRepository->findAll()
        ]);
    }

    #[Route('/filtre', name: 'app_evenement_filtre', methods: ['GET'])]
    public function filtre(Request $request, EvenementRepository $evenementRepository, TypeEvenementRepository $typeEvenementRepository, SerializerInterface $serializer): Response
    {
        $type = $typeEvenementRepository->find($request->query->get('id'));

        $events = $evenementRepository->findBy(["TypeEvenement" => $type->getId()]);
        $eventsJson = $serializer->serialize($events, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
             }
         ]);

        return new JsonResponse($eventsJson);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvenementRepository $evenementRepository, ValidatorInterface $validator): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($evenement);
            // dd($errors);
            if (count($errors) > 0) {
                // dd(1);
                $errorsString = (string) $errors;
                // dd($errors);
                return $this->renderForm('evenement/new.html.twig', [
                    'evenement' => $evenement,
                    'form' => $form,
                    'errors' => $errors
                ]);
            }
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
            'errors' => null
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EvenementRepository $evenementRepository, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $errors = $validator->validate($evenement);
            // dd($errors);
            if (count($errors) > 0) {
                dd(1);
                $errorsString = (string) $errors;
                // dd($errors);
                return $this->renderForm('evenement/edit.html.twig', [
                    'evenement' => $evenement,
                    'form' => $form,
                    'errors' => $errors
                ]);
            }
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
            'errors' => null
        ]);
    }

    #[Route('/delete/{id}', name: 'app_evenement_delete')]
    public function delete(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        $evenementRepository->remove($evenement, true);

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
