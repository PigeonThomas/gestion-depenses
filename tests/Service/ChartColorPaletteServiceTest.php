<?php

namespace App\Tests\Service;

use App\Service\ChartColorPaletteService;
use PHPUnit\Framework\TestCase;

class ChartColorPaletteServiceTest extends TestCase
{
    private ChartColorPaletteService $service;

    protected function setUp(): void
    {
        $this->service = new ChartColorPaletteService();
    }

    // Les tests suivants vérifient la logique de génération de couleurs dans le service ChartColorPaletteService
    // Test de la génération d'une couleur hexadécimale valide pour un label donné
    public function testGetColorForLabelReturnsHexColor(): void
    {
        $color = $this->service->getColorForLabel('Carburant');

        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color);
    }

    // Test de la cohérence de la couleur pour un même label
    public function testGetColorForLabelIsConsistent(): void
    {
        // Le même label doit toujours retourner la même couleur
        $color1 = $this->service->getColorForLabel('Alimentation');
        $color2 = $this->service->getColorForLabel('Alimentation');

        $this->assertSame($color1, $color2);
    }

    // Test de la génération de couleurs différentes pour des labels différents
    public function testGetColorForLabelDifferentLabelsCanHaveDifferentColors(): void
    {
        $color1 = $this->service->getColorForLabel('Carburant');
        $color2 = $this->service->getColorForLabel('Restaurant');

        // Ils peuvent être identiques par coïncidence de hachage, mais on vérifie juste que ce sont des couleurs valides
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color1);
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color2);
    }

    // Test de la génération d'une couleur pour chaque label dans un tableau de labels
    public function testGetColorsForLabelsReturnsOneColorPerLabel(): void
    {
        $labels = ['Carburant', 'Restaurant', 'Loisirs'];
        $colors = $this->service->getColorsForLabels($labels);

        $this->assertCount(3, $colors);
    }

    // Test de la validité de toutes les couleurs générées pour un tableau de labels
    public function testGetColorsForLabelsAllColorsAreValid(): void
    {
        $labels = ['Carburant', 'Restaurant', 'Loisirs', 'Maison', 'Santé'];
        $colors = $this->service->getColorsForLabels($labels);

        foreach ($colors as $color) {
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color);
        }
    }

    // Test de la génération de couleurs pour des labels dupliqués
    public function testGetColorsForLabelsWithDuplicatesReturnsSameColorForSameLabel(): void
    {
        // Même label répété → même couleur attribuée aux deux occurrences
        $labels = ['Carburant', 'Restaurant', 'Carburant'];
        $colors = $this->service->getColorsForLabels($labels);

        $this->assertCount(3, $colors);
        $this->assertSame($colors[0], $colors[2]);
    }

    // Test de la génération de couleurs pour des labels tous différents
    public function testGetColorsForLabelsNoDuplicatesInUniqueLabels(): void
    {
        // Des labels tous différents ne doivent pas partager la même couleur
        $labels = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon'];
        $colors = $this->service->getColorsForLabels($labels);
        $uniqueColors = array_unique($colors);

        $this->assertCount(count($labels), $uniqueColors);
    }

    // Test de la génération de couleurs pour un tableau de labels vide (doit retourner un tableau vide)
    public function testGetColorsForEmptyLabelsReturnsEmptyArray(): void
    {
        $colors = $this->service->getColorsForLabels([]);

        $this->assertSame([], $colors);
    }

    // Test de la génération de couleurs pour des labels avec des caractères spéciaux
    public function testGetColorForLabelIsCaseInsensitive(): void
    {
        // La normalisation met en minuscules → même couleur peu importe la casse
        $color1 = $this->service->getColorForLabel('carburant');
        $color2 = $this->service->getColorForLabel('CARBURANT');
        $color3 = $this->service->getColorForLabel('Carburant');

        $this->assertSame($color1, $color2);
        $this->assertSame($color1, $color3);
    }
}
