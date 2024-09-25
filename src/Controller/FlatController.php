<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Entity\Mark;
use App\Form\FlatType;
use App\Form\MarkType;
use App\Repository\FlatRepository;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;

class FlatController extends AbstractController
{
    #[Route('/flat', name: 'flat.index', methods: ['GET'])]
    public function index(FlatRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $cache = new FilesystemAdapter();
        $data = $cache->get('flatCache', function (ItemInterface $item) use($repository){
            $item->expiresAfter(15);
            return $repository->findPublicFlat(null);
        }); 
        $flats = $paginator->paginate(
            $data,
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

       

        $form = $this->createForm(FlatType::class, $flat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $flat = $form->getData();

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


    #[Route('/plat/{id}', 'flat.show', methods: ['GET', 'POST'])]
    public function show(
        Flat $flat, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response {

        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser())
            ->setFlat($flat);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'flat' => $flat
            ]);
            if(!$existingMark) {
                $manager->persist($mark);
            } else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte.'
            );

            return $this->redirectToRoute('flat.show', ['id' => $flat->getId()]);
        }
        return $this->render('pages/flat/show.html.twig', [
            'flat' => $flat,
            'form' => $form->createView()
        ]);
    }
}


