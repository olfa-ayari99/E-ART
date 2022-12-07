<?php

namespace App\Controller;

use App\Repository\OeuvreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {

        return $this->render('index/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findBy([] , [] , 3),
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
