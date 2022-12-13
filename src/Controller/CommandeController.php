<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use DateTimeImmutable;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\OeuvreRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandeRepository $commandeRepository, OeuvreRepository $oeuvreRepository, ValidatorInterface $validator): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $errors = $validator->validate($commande);

            if (count($errors) > 0) {
               
                $errorsString = (string) $errors;
                // dd(1);
                return $this->redirectToRoute('cart_index', ["errors" => $errors], Response::HTTP_SEE_OTHER);
            }
            $session = $request->getSession();
            $cart = $session->get('cart', []);
            if (!empty($cart)) {
                $total = 0;
                foreach ($cart as $id => $quantity) {
                    $commande->addOeuvre($oeuvreRepository->find($id));
                    $oeuvreRepository->find($id)->setQuantity($oeuvreRepository->find($id)->getQuantity() - $quantity);
                    $total += $quantity * $oeuvreRepository->find($id)->getPrice();
                }
                $commande->setTotal($total);
                $session->set('cart', []);
            } else {
                // dd(1);
                $this->addFlash("error_message", "Votre panier est vide");
                return $this->redirectToRoute('cart_index');
            }
            $commande->setCreatedAt(new DateTimeImmutable("now"));
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_pdf', ['id' => $commande->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
            
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/pdf/{id}', name: 'app_commande_pdf', methods: ['GET'])]
    public function pdf(Commande $commande)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commande/pdf.html.twig', [
            'title' => "Welcome to our PDF Test",
            'commande' => $commande
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        
        // Output the generated PDF to Browser (force download)
        return $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository,ValidatorInterface $validator): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $errors = $validator->validate($commande);

            if (count($errors) > 0) {
               
                $errorsString = (string) $errors;

                return $this->renderForm('commande/edit.html.twig', [
                    'commande' => $commande,
                    'form' => $form,
                    "errors" => $errors
                ]);
            }
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
            "errors" => null
        ]);
    }

    #[Route('/delete/{id}', name: 'app_commande_delete')]
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository, EntityManagerInterface $em): Response
    {
        foreach ($commande->getOeuvres() as $id => $oeuvre) {
            $oeuvre->setQuantity($oeuvre->getQuantity() + $commande->getTotal() / $oeuvre->getPrice());
        }
        $em->flush();
        $commandeRepository->remove($commande, true);


        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
