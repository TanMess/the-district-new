<?php

namespace App\Controller;

use App\Repository\FlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FlatController extends AbstractController
{
    #[Route('/flat', name: 'app_flat')]
    public function index(FlatRepository $repository): Response
    {
        $flats = $repository->findAll();
        return $this->render('pages/flat/index.html.twig', [
            'flats' => $flats
        ]);
    }
}
