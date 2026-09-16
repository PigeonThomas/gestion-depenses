<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class SixMonthDepenseGraphService
{
    private ChartBuilderInterface $chartBuilder;
    private DepenseRepository $depenseRepository;

    public function __construct(ChartBuilderInterface $chartBuilder, DepenseRepository $depenseRepository)
    {
        $this->chartBuilder = $chartBuilder;
        $this->depenseRepository = $depenseRepository;
    }

    /**
     * Methode permettant de créer le bar graphique des dépenses des 6 derniers mois
     * @param int $year L'année pour laquelle récupérer les données
     * @param int $month Le mois pour lequel récupérer les données
     * @param int $userId L'id de l'utilisateur pour lequel récupérer les données
     * @return Chart Le graphique des dépenses des 6 derniers mois
     */
    public function sixMonthDepenseGraph(int $year, int $month, int $userId): Chart
    {
        //creation du tableau des labels des 6 derniers mois
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-$i month");
            $labels[] = $date->format('M Y');
        }

        //récupération des données de dépenses des 6 derniers mois pour l'utilisateur
        $amounts =[];
        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-$i month");
            $year = (int) $date->format('Y');
            $month = (int) $date->format('n');
            $amounts[] = $this->depenseRepository->findTotalDepenseByMonth($year, $month, $userId);
        }

        // Création du graphique
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        // Configuration des données du graphique
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Dépenses',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.8)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'data' => $amounts,
                ],
            ],
        ]);

        // Configuration du graphique
        $chart->setOptions([
            // Permet de rendre le graphique responsive
            'maintainAspectRatio' => false, 
            
            // Configuration des axes et des plugins pour améliorer l'affichage du graphique
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'color' => '#333',
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#333',
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
            ],

            // Configuration des plugins pour la légende, le titre et les datalabels du graphique
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => false,
                    'text' => 'Dépenses des 6 derniers mois',
                ],
                'datalabels' => [
                    'display' => true,
                    'color' => '#000',
                    'anchor' => 'end',
                    'align' => 'top',
                    'font' => [
                        'size' => 14,
                    ],
                    'formatter' => function ($value) {
                        return $value . ' €';
                    },
                ],
            ],
        ]);

        return $chart;
    }
}   