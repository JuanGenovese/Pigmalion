<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Grid;
use App\Exception\InvalidCharacterException;
use App\Exception\ContiguousDoorsException;
use App\Exception\OpenPerimeterException;

class Validator
{
    private DoorDetector $doorDetector;

    public function __construct(DoorDetector $doorDetector)
    {
        $this->doorDetector = $doorDetector;
    }

    public function validate(Grid $grid): void
    {
        $height = $grid->getHeight();

        // 1. Character validation
        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                $char = $grid->getCell($r, $c);
                if ($char !== '#' && $char !== ' ') {
                    throw new InvalidCharacterException("Invalid character '{$char}' at ({$r}, {$c}). Only '#' and ' ' are allowed.");
                }
            }
        }

        // 2. Door adjacency & wall gap validation
        // We scan for wall gaps (sequences of spaces flanked by walls) and verify they don't exceed length 1.
        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                if ($grid->isEmpty($r, $c)) {
                    // Check horizontal gap starting at (r, c)
                    if ($grid->isWall($r, $c - 1)) {
                        $len = 0;
                        while ($grid->isEmpty($r, $c + $len)) {
                            $len++;
                        }
                        if ($grid->isWall($r, $c + $len)) {
                            // Flanked horizontally on both sides by walls.
                            // If all spaces in this gap are horizontal doors (have space above and below),
                            // then this sequence of spaces represents a horizontal door.
                            $isDoorSequence = true;
                            for ($i = 0; $i < $len; $i++) {
                                $top = $grid->getCell($r - 1, $c + $i);
                                $bottom = $grid->getCell($r + 1, $c + $i);
                                if ($top !== ' ' || $bottom !== ' ') {
                                    $isDoorSequence = false;
                                    break;
                                }
                            }
                            if ($isDoorSequence) {
                                // A sequence of spaces is a door only if it is not a room row of width 'len'.
                                // A room row of width 'len' is flanked on both sides by vertical walls.
                                $leftFlankingIsVerticalWall = $this->isPartOfVerticalWall($grid, $r, $c - 1);
                                $rightFlankingIsVerticalWall = $this->isPartOfVerticalWall($grid, $r, $c + $len);
                                if ($leftFlankingIsVerticalWall && $rightFlankingIsVerticalWall) {
                                    $isDoorSequence = false;
                                }
                            }
                            if ($isDoorSequence && $len > 1) {
                                throw new ContiguousDoorsException("Contiguous doors / door of width > 1 detected horizontally at row {$r}, col {$c}.");
                            }
                        }
                    }

                    // Check vertical gap starting at (r, c)
                    if ($grid->isWall($r - 1, $c)) {
                        $len = 0;
                        while ($grid->isEmpty($r + $len, $c)) {
                            $len++;
                        }
                        if ($grid->isWall($r + $len, $c)) {
                            // Flanked vertically on both sides by walls.
                            // If all spaces in this gap are vertical doors (have space left and right),
                            // and at least one cell has a wall neighbor on left or right,
                            // then this is a vertical door.
                            $isDoorSequence = true;
                            $hasWallNeighbor = false;
                            for ($i = 0; $i < $len; $i++) {
                                $left = $grid->getCell($r + $i, $c - 1);
                                $right = $grid->getCell($r + $i, $c + 1);
                                if ($left !== ' ' || $right !== ' ') {
                                    $isDoorSequence = false;
                                    break;
                                }
                                if ($left === '#' || $right === '#') {
                                    $hasWallNeighbor = true;
                                }
                            }
                            if ($isDoorSequence && $hasWallNeighbor && $len > 1) {
                                throw new ContiguousDoorsException("Contiguous doors / door of height > 1 detected vertically at row {$r}, col {$c}.");
                            }
                        }
                    }
                }
            }
        }

        // 3. Perimeter validation
        // Any space cell that is not a door must not have any out-of-bounds neighbor.
        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                if ($grid->isEmpty($r, $c)) {
                    if (!$this->doorDetector->isDoor($grid, $r, $c)) {
                        // Check if any neighbor is out of bounds
                        $neighbors = [
                            [$r - 1, $c],
                            [$r + 1, $c],
                            [$r, $c - 1],
                            [$r, $c + 1]
                        ];
                        foreach ($neighbors as [$nr, $nc]) {
                            if ($grid->getCell($nr, $nc) === null) {
                                throw new OpenPerimeterException("Open perimeter detected: room space at ({$r}, {$c}) is exposed to the outside.");
                            }
                        }
                    }
                }
            }
        }
    }

    private function isPartOfVerticalWall(Grid $grid, int $r, int $c): bool
    {
        return $grid->isWall($r - 1, $c) || $grid->isWall($r + 1, $c);
    }
}
