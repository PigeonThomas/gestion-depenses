<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Repository\DepenseRepository;
use App\Service\CategorieDepenseGraphService;
use App\Service\MagasinCategorieGraphService;

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
        DepenseRepository $depenseRepository, 
        CategorieDepenseGraphService $categorieDepenseGraphService,
        MagasinCategorieGraphService $magasinCategorieGraphService
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
        
        $chartCategorie = $categorieDepenseGraphService->categorieDepenseGraph($annee, $mois, $user->getId());
        $chartMagasin = $magasinCategorieGraphService->magasinDepenseGraph($annee, $mois, $user->getId());

        return $this->render('home/dashboard.html.twig', [
            'title' => 'Dashboard',
            'totalDepenseActualMonth' => $totalDepenseActualMonth,
            'totalDepenseLastMonth' => $totalDepenseLastMonth,
            'chartCategorie' => $chartCategorie,
            'chartMagasin' => $chartMagasin,
        ]);
    }
}
