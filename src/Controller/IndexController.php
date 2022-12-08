<?php

namespace App\Controller;

use App\Repository\OeuvreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
<<<<<<< HEAD
use App\Repository\EvenementRepository;
=======
>>>>>>> bfaa3b66a53d775195d534f4717f37a673aaddb7
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
<<<<<<< HEAD
    public function index(OeuvreRepository $oeuvreRepository , EvenementRepository $evenementRepository): Response
=======
    public function index(OeuvreRepository $oeuvreRepository): Response
>>>>>>> bfaa3b66a53d775195d534f4717f37a673aaddb7
    {

        return $this->render('index/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findBy([] , [] , 3),
<<<<<<< HEAD
            'events' => $evenementRepository->findBy( [] , [] , 3),
=======
>>>>>>> bfaa3b66a53d775195d534f4717f37a673aaddb7
        ]);
    }

    #[Route('/addtocart', name: 'add_to_cart', methods: ['GET'])]
    public function addToCart(Request $request)
    {
        $id = $request->query->get('oeuvre');
        $qty = $request->query->get('qty');

        $session = $request->getSession();

        $cart = $session->get("cart" , []);

        if(!empty($cart[$id])){
            $cart[$id] += $qty ;
        }else{
            $cart[$id] = $qty ;
        }
        $session->set('cart' , $cart);
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart', name: 'cart_index', methods: ['GET'])]
    public function cartIndex(Request $request , OeuvreRepository $oeuvreRepository)
    {
        $session = $request->getSession();

        $cart = $session->get("cart" , []);

        $cartWithData = [] ;
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                "oeuvre" => $oeuvreRepository->find($id),
                "quantity" => $quantity
            ];
        }
        
        $session->set('cart' , $cart);
        
        return $this->render('oeuvre/cart.html.twig' , [
            "cartItems" => $cartWithData,
            "errors" =>null
        ]);
    }

    #[Route('/removecart/{id}', name: 'cart_delete', methods: ['GET'])]
    public function cartDelete($id ,Request $request , OeuvreRepository $oeuvreRepository)
    {
        $session = $request->getSession();

        $cart = $session->get("cart" , []);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }
        
        $session->set('cart' , $cart);
        
        return $this->redirectToRoute('cart_index');
    }
}
