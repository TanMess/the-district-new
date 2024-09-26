<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Repository\FlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;



class BasketController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    #[Route('/panier', name: 'basket_index', methods: ['GET'])]
    public function index(SessionInterface $session, FlatRepository $flatRepository)
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $flat = $flatRepository->find($id);
            $dataPanier[] = [
                "plat" => $flat,
                "quantite" => $quantite
            ];
            $total += $flat->getPrice() * $quantite;
        }

        return $this->render('basket/index.html.twig', compact("dataPanier", "total"));
    }

    
      #[Route('/add/{id}', name: 'add', methods: ['GET'])]

     
    public function add(Flat $flat, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $flat->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("basket_index");
    }

   
    #[Route('/remove/{id}', name: 'remove', methods: ['GET'])]

    public function remove(Flat $flat, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $flat->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("basket_index");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    #[Route('/delete/{id}', name: 'delete', methods: ['GET'])]

    public function delete(Flat $flat, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $flat->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("basket_index");
    }

    /**
     * @Route("/delete", name="delete_all")
     */
    #[Route('/delete', name: 'delete_all', methods: ['GET'])]

    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("basket_index");
    }

}
