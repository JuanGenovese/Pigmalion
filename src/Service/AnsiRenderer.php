<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Grid;

class AnsiRenderer
{
    private const ANSI_BG_COLORS = [
        196, // Red
        39,  // Light Blue
        46,  // Green
        220, // Yellow
        201, // Pink/Magenta
        208, // Orange
        93,  // Purple
        51,  // Cyan
        130, // Brown
        28,  // Dark Green
        27,  // Dark Blue
    ];

    /**
     * @param array<int, array<int, int|null>> $roomMap
     */
    public function render(Grid $grid, array $roomMap): string
    {
        $output = '';
        $height = $grid->getHeight();

        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                $char = $grid->getCell($r, $c);
                $roomId = $roomMap[$r][$c] ?? null;

                if ($roomId !== null) {
                    $colorCode = self::ANSI_BG_COLORS[($roomId - 1) % count(self::ANSI_BG_COLORS)];
                    $output .= "\e[48;5;{$colorCode}m \e[0m";
                } else {
                    $output .= $char;
                }
            }
            if ($r < $height - 1) {
                $output .= "\n";
            }
        }

        return $output;
    }
}
