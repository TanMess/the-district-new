<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\FlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
        #[Route('/', 'app.accueil', methods: ['GET'])]
        public function index(
            CategoryRepository $category,
            FlatRepository $flat
        ): Response {
            return $this->render('pages/home.html.twig', [
                'categorys' => $category->findPublicCategory(3),
                'flats' => $flat->findPublicFlat(3)
            ]);
        }
    }
    
    

