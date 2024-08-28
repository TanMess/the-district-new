<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Form\FlatType;
use App\Repository\FlatRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FlatController extends AbstractController
{
    #[Route('/flat', name: 'flat', methods: ['GET'])]
    public function index(FlatRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $flats = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), 
            10 
        );
        return $this->render('pages/flat/index.html.twig', [
            'flats' => $flats
        ]);
    }
    #[Route('/plat/nouveau', 'plat.new', methods: ['GET', 'POST'])]
    public function new(): Response{

        $flat = new Flat();
        $form = $this->createForm(FlatType::class, $flat);
        return $this->render('pages/flat/new.html.twig', [
            'form' =>$form->createView()
        ]);




    }
}
