<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Repository\DepenseRepository;
use App\Service\CategorieDepenseGraphService;
use App\Service\MagasinCategorieGraphService;
use App\Service\SixMonthDepenseGraphService;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(
        Request $request,
        DepenseRepository $depenseRepository, 
        CategorieDepenseGraphService $categorieDepenseGraphService,
        MagasinCategorieGraphService $magasinCategorieGraphService,
        SixMonthDepenseGraphService $sixMonthDepenseGraphService
        ): Response
    {
        // Vérifie que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos dépenses.');
        }
        
        $now = new \DateTime();
        $annee = (int) $now->format('Y');
        $mois = (int) $now->format('n');

        $lastMonthDate = new \DateTime('first day of last month');
        $anneeLastMonth = (int) $lastMonthDate->format('Y');
        $moisLastMonth  = (int) $lastMonthDate->format('n');

        $totalDepenseActualMonth = $depenseRepository->findTotalDepenseByMonth($annee, $mois, $user->getId());
        $totalDepenseLastMonth = $depenseRepository->findTotalDepenseByMonth($anneeLastMonth, $moisLastMonth, $user->getId());

        // Détermine la période sélectionnée pour les graphiques catégorie et magasin
        $period = $request->query->get('period', 'current');
        if ($period === 'last') {
            $anneeGraph = $anneeLastMonth;
            $moisGraph  = $moisLastMonth;
        } else {
            $period = 'current';
            $anneeGraph = $annee;
            $moisGraph  = $mois;
        }

        $chartCategorie = $categorieDepenseGraphService->categorieDepenseGraph($anneeGraph, $moisGraph, $user->getId());
        $chartMagasin = $magasinCategorieGraphService->magasinDepenseGraph($anneeGraph, $moisGraph, $user->getId());
        $chartSixMonth = $sixMonthDepenseGraphService->sixMonthDepenseGraph($annee, $mois, $user->getId());

        // Pré-calcul des données des 6 mois pour les donuts (mis à jour au clic côté JS, sans rechargement)
        $sixMonthDonutData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-$i month");
            $y = (int) $date->format('Y');
            $m = (int) $date->format('n');
            $sixMonthDonutData[] = [
                'label'     => $date->format('M Y'),
                'categorie' => $categorieDepenseGraphService->getCategorieData($y, $m, $user->getId()),
                'magasin'   => $magasinCategorieGraphService->getMagasinData($y, $m, $user->getId()),
            ];
        }

        return $this->render('home/dashboard.html.twig', [
            'title' => 'Dashboard',
            'totalDepenseActualMonth' => $totalDepenseActualMonth,
            'totalDepenseLastMonth' => $totalDepenseLastMonth,
            'chartCategorie' => $chartCategorie,
            'chartMagasin' => $chartMagasin,
            'chartSixMonth' => $chartSixMonth,
            'period' => $period,
            'sixMonthDonutData' => $sixMonthDonutData,
        ]);
    }
}
