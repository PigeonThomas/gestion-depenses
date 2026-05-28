<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use App\Service\ChartColorPaletteService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MagasinCategorieGraphService
{
    private ChartBuilderInterface $chartBuilder;
    private DepenseRepository $depenseRepository;
    private ChartColorPaletteService $chartColorPaletteService;

    public function __construct(
        ChartBuilderInterface $chartBuilder,
        DepenseRepository $depenseRepository,
        ChartColorPaletteService $chartColorPaletteService
    )
    {
        $this->chartBuilder = $chartBuilder;
        $this->depenseRepository = $depenseRepository;
        $this->chartColorPaletteService = $chartColorPaletteService;
    }

    /**
     * Methode permettant de créer le graphique des dépenses par magasin
     * @param int $year L'année pour laquelle récupérer les données
     * @param int $month Le mois pour lequel récupérer les données
     * @param int $userId L'id de l'utilisateur pour lequel récupérer les données
     * @return Chart Le graphique des dépenses par magasin
     */
    public function magasinDepenseGraph(int $year, int $month, int $userId): Chart
    {
        $data = $this->depenseRepository->findTotalByMagasinAndMonth($year, $month, $userId);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $labels = array_column($data, 'magasin');
        $total = array_column($data, 'total');
        $colors = $this->chartColorPaletteService->getColorsForLabels($labels);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Dépenses €',
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'data' => $total,
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