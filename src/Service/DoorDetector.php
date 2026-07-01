<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Grid;

class DoorDetector
{
    public function isDoor(Grid $grid, int $r, int $c): bool
    {
        if (!$grid->isEmpty($r, $c)) {
            return false;
        }

        $left = $grid->getCell($r, $c - 1);
        $right = $grid->getCell($r, $c + 1);
        $top = $grid->getCell($r - 1, $c);
        $bottom = $grid->getCell($r + 1, $c);

        if ($left === null || $right === null || $top === null || $bottom === null) {
            return false;
        }

        // Flanked horizontally (left and right are walls)
        if ($left === '#' && $right === '#') {
            $leftIsVertical = $grid->isWall($r - 1, $c - 1) || $grid->isWall($r + 1, $c - 1);
            $rightIsVertical = $grid->isWall($r - 1, $c + 1) || $grid->isWall($r + 1, $c + 1);
            if (!($leftIsVertical && $rightIsVertical)) {
                return true;
            }
        }

        // Flanked vertically (top and bottom are walls)
        if ($top === '#' && $bottom === '#') {
            $topIsHorizontal = $grid->isWall($r - 1, $c - 1) || $grid->isWall($r - 1, $c + 1);
            $bottomIsHorizontal = $grid->isWall($r + 1, $c - 1) || $grid->isWall($r + 1, $c + 1);
            if (!($topIsHorizontal && $bottomIsHorizontal)) {
                return true;
            }
        }

        return false;
    }
}
