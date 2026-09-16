<?php

namespace App\Service;

use App\Repository\VehiculeRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class SixMonthKmVehiculeGraphService
{
    private ChartBuilderInterface $chartBuilder;
    private VehiculeRepository $vehiculeRepository;
    private DistanceVehiculeService $distanceVehiculeService;
    private ChartColorPaletteService $chartColorPaletteService;

    public function __construct(
        ChartBuilderInterface $chartBuilder,
        VehiculeRepository $vehiculeRepository,
        DistanceVehiculeService $distanceVehiculeService,
        ChartColorPaletteService $chartColorPaletteService
    ) {
        $this->chartBuilder = $chartBuilder;
        $this->vehiculeRepository = $vehiculeRepository;
        $this->distanceVehiculeService = $distanceVehiculeService;
        $this->chartColorPaletteService = $chartColorPaletteService;
    }

    /**
     * Méthode permettant de créer le bar graphique empilé des km parcourus par véhicule sur les 6 derniers mois
     * @param int $userId L'id de l'utilisateur pour lequel récupérer les données
     * @return Chart Le graphique des km parcourus des 6 derniers mois, empilé par véhicule
     */
    public function sixMonthKmVehiculeGraph(int $userId): Chart
    {
        // Génération des 6 derniers mois (année/mois + label affiché)
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-$i month");
            $months[] = [
                'annee' => (int) $date->format('Y'),
                'mois' => (int) $date->format('n'),
                'label' => $date->format('M Y'),
            ];
        }

        $vehicules = $this->vehiculeRepository->findByUserId($userId);
        $labels = array_map(fn ($vehicule) => $vehicule->getSurnomVehicule(), $vehicules);
        $colors = $this->chartColorPaletteService->getColorsForLabels($labels);

        // Un dataset par véhicule, contenant les km parcourus pour chacun des 6 mois
        $datasets = [];
        foreach ($vehicules as $index => $vehicule) {
            $data = [];
            foreach ($months as $month) {
                $data[] = (int) $this->distanceVehiculeService->calculateDistance(
                    $vehicule->getId(),
                    $month['annee'],
                    $month['mois'],
                    $userId
                );
            }

            $datasets[] = [
                'label' => $vehicule->getSurnomVehicule(),
                'backgroundColor' => $colors[$index],
                'borderColor' => $colors[$index],
                'borderWidth' => 1,
                'data' => $data,
            ];
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => array_column($months, 'label'),
            'datasets' => $datasets,
        ]);

        $chart->setOptions([
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'ticks' => [
                        'color' => '#333',
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'color' => '#333',
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'color' => '#333',
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
                'datalabels' => [
                    'display' => true,
                    'color' => '#fff',
                    'font' => [
                        'size' => 12,
                    ],
                    'formatter' => function ($value) {
                        return $value > 0 ? $value . ' km' : '';
                    },
                ],
            ],
        ]);

        return $chart;
    }
}
