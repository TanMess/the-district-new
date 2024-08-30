<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Form\FlatType;
use App\Repository\FlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FlatController extends AbstractController
{
    #[Route('/flat', name: 'flat.index', methods: ['GET'])]
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
    #[Route('/plat/nouveau', name: 'flat.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] string $photodir): Response
    {

        $flat = new Flat();
        $form = $this->createForm(FlatType::class, $flat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $flat = $form->getData();
            if ($image = $form['image']->getData()) {
                $filename = uniqid() . '.' . $image->guessExtension();
                $image->move($photodir, $filename);
            }

            $entityManager->persist($flat);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre plat a été ajouter avec succès !'
            );
            return $this->redirectToRoute('flat.index');
        }
        return $this->render('pages/flat/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/plat/edition/{id}', name: 'flat.edit', methods: ['GET', 'POST'])]
    public function edit(FlatRepository $repository, int $id): Response
    {
        $flat = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(FlatType::class, $flat);

        return $this->render('pages/flat/edit.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
