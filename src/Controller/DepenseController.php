<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\User;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use App\Service\KilometrageValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/depense')]
#[IsGranted('ROLE_USER')]
final class DepenseController extends AbstractController
{
    #[Route(name: 'app_depense_index', methods: ['GET'])]
    public function index(
        DepenseRepository $depenseRepository,
        PaginatorInterface $paginator,
        Request $request
        ): Response
    {
        // Vérifie que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos dépenses.');
        }

        // Récupère les dépenses de l'utilisateur avec pagination
        $page = $request->query->getInt('page', 1);
        $limit = 8; // Nombre de dépenses par page
        $depenses = $paginator->paginate(
            $depenseRepository->queryByUserId($user->getId()),
            $page,
            $limit,
            [
                'distinct' => true, // Assure que les résultats sont distincts pour éviter les doublons
                'sortFieldAllowList' => ['d.date_depense', 'd.montant_depense', 'c.nom_categorie'], // Champs autorisés pour le tri
                'defaultSortFieldName' => 'd.date_depense', // Champ de tri par défaut
                'defaultSortDirection' => 'desc', // Direction de tri par défaut
            ]
        );

        return $this->render('depense/index.html.twig', [
            'title' => 'Mes dépenses',
            'depenses' => $depenses,
        ]);
    }

    #[Route('/new', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, KilometrageValidationService $kmValidation): Response
    {
        //Vérifie que l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une dépense.');
        }

        $depense = new Depense();
        $form = $this->createForm(DepenseType::class, $depense, [
            'user' => $user, // Passe l'utilisateur connecté au formulaire pour filtrer les choix
        ]);
        $form->handleRequest($request);

        //Vérifie que le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $kmError = $kmValidation->validate($depense);

            // Si une erreur de validation du kilométrage est détectée, affiche un message d'erreur et redirige vers le formulaire de création de dépense
            if ($kmError !== null) {
                $this->addFlash('danger', $kmError);

                return $this->render('depense/new.html.twig', [
                    'title' => 'Créer une dépense',
                    'depense' => $depense,
                    'form' => $form,
                ]);
            }

            $depense->setUser($user);
            $entityManager->persist($depense);
            $entityManager->flush();
            $this->addFlash('success', 'Dépense créée avec succès.');

            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depense/new.html.twig', [
            'title' => 'Créer une dépense',
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_depense_show', methods: ['GET'])]
    public function show(Depense $depense): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //Vérifie que l'utilisateur connecté est le propriétaire de la dépense
        if ($depense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette dépense.');
        }
        
        return $this->render('depense/show.html.twig', [
            'title' => 'Détails de la dépense',
            'depense' => $depense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depense, EntityManagerInterface $entityManager, KilometrageValidationService $kmValidation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //Vérifie que l'utilisateur connecté est le propriétaire de la dépense
        if ($depense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette dépense.');
        }

        $form = $this->createForm(DepenseType::class, $depense, [
            'user' => $this->getUser(), // Passe l'utilisateur connecté au formulaire pour filtrer les choix
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $kmError = $kmValidation->validate($depense);
            if ($kmError !== null) {
                $this->addFlash('danger', $kmError);

                return $this->render('depense/edit.html.twig', [
                    'title' => 'Modifier la dépense',
                    'depense' => $depense,
                    'form' => $form,
                ]);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Dépense modifiée avec succès.');

            return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depense/edit.html.twig', [
            'title' => 'Modifier la dépense',
            'depense' => $depense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, Depense $depense, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //Vérifie que l'utilisateur connecté est le propriétaire de la dépense
        if ($depense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette dépense.');
        }

        if ($this->isCsrfTokenValid('delete'.$depense->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($depense);
            $entityManager->flush();
            $this->addFlash('success', 'Dépense supprimée avec succès.');
        }

        return $this->redirectToRoute('app_depense_index', [], Response::HTTP_SEE_OTHER);
    }
}
