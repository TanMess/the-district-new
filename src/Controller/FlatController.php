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
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] $photodir): Response
    {

        $flat = new Flat();
        $form = $this->createForm(FlatType::class, $flat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $flat = $form->getData();
            if ($image = $form['image']->getData()) {
                $filename = uniqid() . '.' . $image->guessExtension();
                $image->move($photodir, $filename);
                $flat->setImage($filename);
            }

            $entityManager->persist($flat);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre plat a été ajouté avec succès !'
            );
            return $this->redirectToRoute('flat.index');
        }
        return $this->render('pages/flat/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/plat/edition/{id}', name: 'flat.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FlatRepository $repository, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] $photodir, int $id): Response
    {
        // Récupérer l'entité existante
        $flat = $repository->findOneBy(["id" => $id]);

        // Si l'entité n'est pas trouvée, renvoyer une erreur 404
        if (!$flat) {
            throw $this->createNotFoundException('Le plat demandé n\'existe pas.');
        }

        // Sauvegarder l'ancien nom de fichier pour le cas où l'utilisateur ne télécharge pas une nouvelle image
        $oldImageFilename = $flat->getImage();

        $form = $this->createForm(FlatType::class, $flat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $form['image']->getData()) {
                // Si une nouvelle image est téléchargée, on remplace l'ancienne
                $filename = uniqid() . '.' . $image->guessExtension();
                $image->move($photodir, $filename);
                $flat->setImage($filename);
            } else {
                // Si aucune nouvelle image n'est téléchargée, on conserve l'ancienne
                $flat->setImage($oldImageFilename);
            }

            $entityManager->persist($flat);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre plat a été mis à jour avec succès !'
            );

            return $this->redirectToRoute('flat.index');
        }

        return $this->render('pages/flat/edit.html.twig', [
            'form' => $form->createView(),
            'flat' => $flat
        ]);
    }

    #[Route('/plat/suppression/{id}', name: 'flat.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Flat $flat): Response
    {
        $manager->remove($flat);
        $manager->flush();
        
        $this->addFlash(
            'success',
            'Votre plat a été supprimé avec succès !'
        );

        return $this->redirectToRoute('flat.index');
    }
}
