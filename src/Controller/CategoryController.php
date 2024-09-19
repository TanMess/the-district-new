<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Mark;
use App\Form\CategoryType;
use App\Form\MarkType;
use App\Repository\CategoryRepository;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category.index')]
    public function index(CategoryRepository $repository, PaginatorInterface $paginator, Request $request): Response

    {
        $categorys = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/category/index.html.twig', [
            'categorys' => $categorys
        ]);
    }

    #[Route('/category/nouveau', name: 'category.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre catégorie a été ajouté avec succès !'
            );
            return $this->redirectToRoute('category.index');
        }
        return $this->render('pages/category/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/category/edition/{id}', name: 'category.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryRepository $repository, EntityManagerInterface $entityManager, $id): Response
    {
        // Récupérer l'entité existante
        $category = $repository->findOneBy(["id" => $id]);

        // Si l'entité n'est pas trouvée, renvoyer une erreur 404
        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandé n\'existe pas.');
        }

        

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre catégorie a été mis à jour avec succès !'
            );

            return $this->redirectToRoute('category.index');
        }

        return $this->render('pages/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/category/suppression/{id}', name: 'category.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Category $category): Response
    {
        $manager->remove($category);
        $manager->flush();
        
        $this->addFlash(
            'success',
            'Votre catégorie a été supprimé avec succès !'
        );

        return $this->redirectToRoute('category.index');
        }


    
}