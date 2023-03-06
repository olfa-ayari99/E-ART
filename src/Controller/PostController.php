<?php

namespace App\Controller;
use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginationInterface;
use App\Entity\Notification;



class PostController extends AbstractController
{
    #[Route('/post', name: 'app_posts')]
    public function listPost(PostRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $repository->findAll();
        $posts = $paginator->paginate( $posts, $request->query->getInt('page', 1), 5);
        return $this->render('Post/post.html.twig', array("listPost" => $posts));
    }

    #[Route('/addPost', name: 'app_add_post')]
    public function addPost(Request $request, ManagerRegistry $doctrine,)
    {
        $post = new Post(); //création d'une instance post pour l'ajouter dans la bdd.
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute("app_posts");
        }
        return $this->renderForm("Post/add_post.html.twig", array("postForm" => $form));
    }

    #[Route('/detailPost/{id}', name: 'app_detail_post')]
    public function detailPosts($id, PostRepository  $repository)
    {
        $post = $repository->find($id);

        return $this->render("Post/detail_post.html.twig", array("post" => $post));
    }

    #[Route('/myPosts', name: 'app_my_posts')]
    public function myPosts(PostRepository  $repository)
    {
        $posts = $repository->findAll();
        return $this->render("Post/my_posts.html.twig", array("listPost" => $posts));
    }

    #[Route('/deletePost/{id}', name: 'app_delete_post')]
    public function deletePost ($id, PostRepository  $repository, ManagerRegistry $doctrine ): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $post = $repository->find($id);
        $em = $doctrine->getManager(); // $em=$this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute("app_my_posts");
    }
    #[Route('/editPost/{id}', name: 'app_edit_post')]
    public function editPost ($id, PostRepository  $repository, Request $request,ManagerRegistry $doctrine )
    {
        $post= $repository->find($id);
        $form= $this->createForm(PostFormType::class,$post);
        $form->handleRequest($request) ;

        if($form->isSubmitted()){
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded

            $em = $doctrine->getManager();
            $em->flush();
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_my_posts");
        }
        return $this->renderForm("Post/edit_post.html.twig", array("postForm" => $form, "post"=>$post));
    }

}







