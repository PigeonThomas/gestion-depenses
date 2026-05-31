<?php

namespace App\Controller;

use App\Entity\Magasin;
use App\Entity\User;
use App\Form\MagasinType;
use App\Repository\MagasinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/magasin')]
#[IsGranted('ROLE_USER')]
final class MagasinController extends AbstractController
{
    #[Route(name: 'app_magasin_index', methods: ['GET'])]
    public function index(MagasinRepository $magasinRepository, PaginatorInterface $paginator, Request $request): Response
    {
        //Vérifie que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos magasins.');
        }

        // Récupère les magasins de l'utilisateur avec pagination
        $page = $request->query->getInt('page', 1);
        $limit = 8; // Nombre de magasins par page
        $magasins = $paginator->paginate(
            $magasinRepository->queryByUserId($user->getId()),
            $page,
            $limit,
            [
                'distinct' => true, // Assure que les résultats sont distincts pour éviter les doublons
                'sortFieldAllowList' => ['m.nom_magasin', 'm.online_magasin', 'm.createdAt'], // Champs autorisés pour le tri
                'defaultSortFieldName' => 'm.createdAt', // Champ de tri par défaut
                'defaultSortDirection' => 'desc', // Direction de tri par défaut
            ]
        );

        return $this->render('magasin/index.html.twig', [
            'title' => 'Mes magasins',
            'magasins' => $magasins,
        ]);
    }

    #[Route('/new', name: 'app_magasin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Vérifie que l'utilisateur est connecté        
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer un magasin.');
        }

        $magasin = new Magasin();
        $form = $this->createForm(MagasinType::class, $magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $magasin->setUser($user);
            $entityManager->persist($magasin);
            $entityManager->flush();
            $this->addFlash('success', 'Magasin créé avec succès.');

            return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('magasin/new.html.twig', [
            'title' => 'Ajouter un magasin',
            'magasin' => $magasin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_magasin_show', methods: ['GET'])]
    public function show(Magasin $magasin): Response
    {
        //Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        //Vérifie que l'utilisateur connecté est le propriétaire du magasin
        if ($magasin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce magasin.');
        }

        return $this->render('magasin/show.html.twig', [
            'title' => 'Détails du magasin',
            'magasin' => $magasin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_magasin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Magasin $magasin, EntityManagerInterface $entityManager): Response
    {
        //Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        //Vérifie que l'utilisateur connecté est le propriétaire du magasin
        if ($magasin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce magasin.');
        }

        $form = $this->createForm(MagasinType::class, $magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Magasin modifié avec succès.');
            return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('magasin/edit.html.twig', [
            'title' => 'Modifier le magasin',
            'magasin' => $magasin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_magasin_delete', methods: ['POST'])]
    public function delete(Request $request, Magasin $magasin, EntityManagerInterface $entityManager): Response
    {
        //Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        //Vérifie que l'utilisateur connecté est le propriétaire du magasin
        if ($magasin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce magasin.');
        }

        if ($this->isCsrfTokenValid('delete'.$magasin->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($magasin);
            $entityManager->flush();
            $this->addFlash('success', 'Magasin supprimé avec succès.');
        }

        return $this->redirectToRoute('app_magasin_index', [], Response::HTTP_SEE_OTHER);
    }
}
