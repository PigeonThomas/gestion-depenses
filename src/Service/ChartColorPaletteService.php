<?php

namespace App\Service;

class ChartColorPaletteService
{
    private const PALETTE = [
        '#264653',
        '#2A9D8F',
        '#E9C46A',
        '#F4A261',
        '#E76F51',
        '#577590',
        '#43AA8B',
        '#90BE6D',
        '#F9C74F',
        '#F8961E',
        '#F3722C',
        '#F94144',
        '#277DA1',
        '#4D908E',
        '#355070',
        '#7B2CBF',
        '#5A189A',
        '#9D4EDD',
        '#C77DFF',
        '#4895EF',
        '#4CC9F0',
        '#4361EE',
        '#3A0CA3',
        '#B5179E',
        '#F72585',
        '#D62828',
        '#F77F00',
        '#FCBF49',
        '#003049',
        '#669BBC',
        '#386641',
        '#6A994E',
    ];

    /**
     * @param string[] $labels
     * @return string[]
     */
    public function getColorsForLabels(array $labels): array
    {
        $uniqueLabels = array_values(array_unique($labels)); // Get unique labels while preserving order
        $paletteIndexesByLabel = $this->buildPaletteIndexes($uniqueLabels); // Build a mapping of labels to palette indexes

        // Map each label to its corresponding color based on the palette index
        return array_map(
            fn (string $label): string => self::PALETTE[$paletteIndexesByLabel[$label]], 
            $labels
        );
    }

    /**
     * Get a consistent color for a given label.
     * @param string $label The label to get the color for.
     * @return string The color associated with the label.
     */
    public function getColorForLabel(string $label): string
    {
        $paletteSize = count(self::PALETTE);
        $index = $this->getPreferredPaletteIndex($label) % $paletteSize;

        return self::PALETTE[$index];
    }

    /**
     * @param string[] $labels
     * @return array<string, int>
     */
    private function buildPaletteIndexes(array $labels): array
    {
        $paletteSize = count(self::PALETTE); // Get the size of the color palette
        $orderedLabels = $labels; // Preserve the original order of labels

        // Sort labels by their preferred palette index, then alphabetically for labels with the same index
        usort($orderedLabels, function (string $left, string $right): int {
            $leftIndex = $this->getPreferredPaletteIndex($left); // Get the preferred palette index for the left label
            $rightIndex = $this->getPreferredPaletteIndex($right); // Get the preferred palette index for the right label

            // If both labels have the same preferred index, sort them alphabetically
            if ($leftIndex === $rightIndex) {
                return strcmp($this->normalizeLabel($left), $this->normalizeLabel($right));
            }

            return $leftIndex <=> $rightIndex;
        });

        $usedIndexes = [];
        $indexesByLabel = [];

        // Assign palette indexes to labels, trying to use the preferred index first and then finding the next available index
        foreach ($orderedLabels as $label) {
            $preferredIndex = $this->getPreferredPaletteIndex($label);
            $selectedIndex = $preferredIndex;

            while (isset($usedIndexes[$selectedIndex]) && count($usedIndexes) < $paletteSize) {
                $selectedIndex = ($selectedIndex + 1) % $paletteSize;
            }

            $usedIndexes[$selectedIndex] = true;
            $indexesByLabel[$label] = $selectedIndex;
        }

        return $indexesByLabel;
    }

    /**
     * Get a consistent palette index for a given label.
     * @param string $label The label to get the palette index for.
     * @return int The palette index associated with the label.
     */
    private function getPreferredPaletteIndex(string $label): int
    {
        return abs(crc32($this->normalizeLabel($label))) % count(self::PALETTE);
    }

    /**
     * Normalize a label by trimming whitespace and converting it to lowercase.
     * @param string $label The label to normalize.
     * @return string The normalized label.
     */
    private function normalizeLabel(string $label): string
    {
        return mb_strtolower(trim($label));
    }
}