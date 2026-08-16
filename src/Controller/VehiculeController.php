<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vehicule;
use App\Service\VidangeVehiculeService;
use App\Service\DistributionVehiculeService;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use App\Repository\DepenseRepository;
use App\Service\DistanceVehiculeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vehicule')]
#[IsGranted('ROLE_USER')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(
        VehiculeRepository $vehiculeRepository, 
        DepenseRepository $depenseRepository, 
        DistanceVehiculeService $distanceVehiculeService,
        ): Response
    {
        // Vérifie que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos véhicules.');
        }

        $now = new \DateTime();
        $annee = (int) $now->format('Y');
        $mois = (int) $now->format('n');

        $lastMonthDate = new \DateTime('first day of last month');
        $anneeLastMonth = (int) $lastMonthDate->format('Y');
        $moisLastMonth  = (int) $lastMonthDate->format('n');

        $totalEssenceActualMonth = $depenseRepository->findTotalEssenceByMonth($annee, $mois, $user->getId());
        $totalEssenceLastMonth = $depenseRepository->findTotalEssenceByMonth($anneeLastMonth, $moisLastMonth, $user->getId());
        
        $totalReparationActualMonth = $depenseRepository->findTotalReparationByMonth($annee, $mois, $user->getId());
        $totalReparationLastMonth = $depenseRepository->findTotalReparationByMonth($anneeLastMonth, $moisLastMonth, $user->getId());

        $totalDistance = 0;
        foreach ($vehiculeRepository->findByUserId($user->getId()) as $vehicule) {
            $totalDistance += (int) $distanceVehiculeService->calculateDistance($vehicule->getId(), $annee, $mois, $user->getId());
        }

        $totalDistanceOfYear = 0;
        foreach ($vehiculeRepository->findByUserId($user->getId()) as $vehicule) {
            $totalDistanceOfYear += (int) $distanceVehiculeService->calculateDistanceYear($vehicule->getId(), $annee, $user->getId());
        }

        return $this->render('vehicule/index.html.twig', [
            'title' => 'Véhicules',
            'vehicules' => $vehiculeRepository->findByUserId($user->getId()),
            'mois' => $mois,
            'annee' => $annee,
            'totalEssenceActualMonth' => $totalEssenceActualMonth,
            'totalEssenceLastMonth' => $totalEssenceLastMonth,
            'totalReparationActualMonth' => $totalReparationActualMonth,
            'totalReparationLastMonth' => $totalReparationLastMonth,
            'totalDistance' => $totalDistance, // Pass the total distance to the template if needed
            'totalDistanceOfYear' => $totalDistanceOfYear,
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Vérifie que l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer un véhicule.');
        }

        $vehicule = new Vehicule();

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicule->setUser($user);
            $entityManager->persist($vehicule);
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule créé avec succès.');

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', [
            'title' => 'Créer un véhicule',
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(
        Vehicule $vehicule, 
        DistanceVehiculeService $distanceVehiculeService, 
        DepenseRepository $depenseRepository, 
        VidangeVehiculeService $vidangeVehiculeService, 
        DistributionVehiculeService $distributionVehiculeService): Response
    {
        //Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à ce véhicule.');
        }
        //Vérifie que l'utilisateur connecté est le propriétaire du véhicule
        if ($vehicule->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce véhicule.');
        }

        $actualKm = $depenseRepository->findLastKmByVehicule($vehicule->getId(), $user->getId());

        $kmParcourusDepuisAchat = $distanceVehiculeService->calculateDistanceSincePurchase($vehicule->getId(), $user->getId());

        $lastReparation = $depenseRepository->findLastReparationByVehiculeAndUser($vehicule->getId(), $user->getId());

        $lastVidange = $depenseRepository->findLastVidangeByVehiculeAndUser($vehicule->getId(), $user->getId());
        $lastDistribution = $depenseRepository->findLastDistributionByVehiculeAndUser($vehicule->getId(), $user->getId());

        $nextVidange = $vidangeVehiculeService->calculateNextVidange($vehicule->getId(), $user->getId());
        $nextDistribution = $distributionVehiculeService->calculateNextDistribution($vehicule->getId(), $user->getId());
        $nextDistributionDate = $distributionVehiculeService->calculateNextDistributionDate($vehicule->getId(), $user->getId());

        return $this->render('vehicule/show.html.twig', [
            'title' => 'Détails du véhicule',
            'vehicule' => $vehicule,
            'actualKm' => $actualKm,
            'kmParcourusDepuisAchat' => $kmParcourusDepuisAchat,
            'consommationMoyenne' => 'To be calculated', // Placeholder for average consumption calculation
            'montantReparation' => $lastReparation?->getMontantDepense(),
            'commentaireReparation' => $lastReparation?->getCommentaireDepense(),
            'dateReparation' => $lastReparation?->getDateDepense(),
            'dateVidange' => $lastVidange?->getDateDepense(),
            'kmVidange' => $lastVidange?->getKmVehicule(),
            'dateDistribution' => $lastDistribution?->getDateDepense(),
            'kmDistribution' => $lastDistribution?->getKmVehicule(),
            'nextVidange' => $nextVidange,
            'nextDistribution' => $nextDistribution,
            'nextDistributionDate' => $nextDistributionDate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        //Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        //Vérifie que l'utilisateur connecté est le propriétaire du véhicule
        if ($vehicule->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce véhicule.');
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule modifié avec succès.');

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/edit.html.twig', [
            'title' => 'Modifier le véhicule',
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        //Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        //Vérifie que l'utilisateur connecté est le propriétaire du véhicule
        if ($vehicule->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce véhicule.');
        }

        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule supprimé avec succès.');
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }
}
