<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use App\Repository\DepenseRepository;
use App\Service\DistanceVehiculeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vehicule')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository, DepenseRepository $depenseRepository, DistanceVehiculeService $distanceVehiculeService): Response
    {
        $now = new \DateTime();
        $annee = (int) $now->format('Y');
        $mois = (int) $now->format('n');

        $totalEssenceActualMonth = $depenseRepository->findTotalEssenceByMonth($annee, $mois);
        $totalEssenceLastMonth = $depenseRepository->findTotalEssenceByMonth($annee, $mois - 1);
        
        $totalReparationActualMonth = $depenseRepository->findTotalReparationByMonth($annee, $mois);
        $totalReparationLastMonth = $depenseRepository->findTotalReparationByMonth($annee, $mois - 1);

        $totalDistance = 0;
        foreach ($vehiculeRepository->findAll() as $vehicule) {
            $totalDistance += (int) $distanceVehiculeService->calculateDistance($vehicule->getId(), $annee, $mois);
        }
        return $this->render('vehicule/index.html.twig', [
            'title' => 'Véhicules',
            'vehicules' => $vehiculeRepository->findAll(),
            'mois' => $mois,
            'annee' => $annee,
            'totalEssenceActualMonth' => $totalEssenceActualMonth,
            'totalEssenceLastMonth' => $totalEssenceLastMonth,
            'totalReparationActualMonth' => $totalReparationActualMonth,
            'totalReparationLastMonth' => $totalReparationLastMonth,
            'totalDistance' => $totalDistance, // Pass the total distance to the template if needed
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', [
            'title' => 'Créer un véhicule',
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'title' => 'Détails du véhicule',
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();

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
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }
}
