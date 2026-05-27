<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class CategorieDepenseGraphService
{
    private ChartBuilderInterface $chartBuilder;
    private DepenseRepository $depenseRepository;

    public function __construct(ChartBuilderInterface $chartBuilder, DepenseRepository $depenseRepository)
    {
        $this->chartBuilder = $chartBuilder;
        $this->depenseRepository = $depenseRepository;
    }

    /**
     * Methode permettant de récupérer les données pour le graphique des dépenses par catégorie
     * @param int $year L'année pour laquelle récupérer les données
     * @param int $month Le mois pour lequel récupérer les données
     * @param int $userId L'id de l'utilisateur pour lequel récupérer les données
     * @return Chart Le graphique des dépenses par catégorie
     */
    public function categorieDepenseGraph(int $year, int $month, int $userId): Chart
    {
        $data = $this->depenseRepository->findTotalByCategoryAndMonth($year, $month, $userId);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $chart->setData([
            'labels' => array_column($data, 'category'),
            'datasets' => [
                [
                    'label' => 'Dépenses €',
                    'backgroundColor' => array_column($data, 'color'),
                    'borderColor' => array_column($data, 'color'),
                    'data' => array_column($data, 'total'),
                    'hoverOffset' => 4,
                ],
            ],
        ]);

        $chart->setOptions([
            'maintainAspectRatio' => false,
        ]);

        return $chart;
    }
}   