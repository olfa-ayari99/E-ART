<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireFormType;
use App\Repository\CommentaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{


    /**
     * @param CommentaireRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/commentaire', name: 'app_commentaires')]
    public function listCommentaire(CommentaireRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $commentaires = $repository->findAll();
        return $this->render('Post/post.html.twig', array('listCommentaire' => $commentaires));
    }


    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return RedirectResponse|Response
     */
    #[Route('/deleteCommentaire/{id}', name: 'app_delete_commentaire')]
    public function deleteCommentaire ($id, CommentaireRepository  $repository, ManagerRegistry $doctrine ): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $commentaire = $repository->find($id);
        $em = $doctrine->getManager(); // $em=$this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute('app_my_commentaires');
    }


    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return RedirectResponse|Response
     */
    #[Route('/addCommentaire', name: 'app_add_commentaire')]
    public function addCommentaire(Request $request, ManagerRegistry $doctrine,): \Symfony\Component\HttpFoundation\RedirectResponse|Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireFormType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('app_send_mail');
        }
        return $this->renderForm("Post/add_commentaire.html.twig", array("commentaireForm" => $form, "commentaire"=>$commentaire));
    }



    #[Route('/my_commentaires', name: 'app_my_commentaires')]
    public function my_commentaires(CommentaireRepository  $repository)
    {
        $commentaires = $repository->findAll();
        return $this->render('Post/my_commentaires.html.twig', array('listCommentaire' => $commentaires));
    }


    #[Route('/editCommentaire/{id}', name: 'app_edit_commentaire')]
    public function editCommetaire ($id, CommentaireRepository  $repository, Request $request,ManagerRegistry $doctrine ): RedirectResponse|Response
    {
        $commentaire= $repository->find($id);
        $form= $this->createForm(CommentaireFormType::class,$commentaire );
        $form->handleRequest($request) ;

        if($form->isSubmitted()){
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            $em = $doctrine->getManager();
            $em->flush();
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_add_commentaire");
        }
        return $this->renderForm("Post/edit_commentaire.html.twig", array("commentaireForm" => $form, "commentaire"=>$commentaire));
    }


    /**
     * @return RedirectResponse
     */
    #[Route('/sendMail', name: 'app_send_mail')]
    public function sendMail()
    {

        $mail = (new Email())
            ->from('mahmoud.lakhal@esprit.tn')
            ->to('mahmoud.lakhal@esprit.tn')
            ->subject('Nouveau commentaire')
            ->html('<p>Ceci est mon message en HTML</p>')
        ;
        $mail->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');
        $transport = Transport::fromDsn('gmail+smtp://mahmoud.lakhal@esprit.tn:motdepasse@default?verify_peer=0');
        $mailer = new Mailer($transport);
        try{
            $mailer->send($mail);
        }catch(TransportExceptionInterface $e){
            dd($e);
        };


        return $this->redirectToRoute('app_add_commentaire');
    }
}
