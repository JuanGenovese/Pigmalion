<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Grid;

class RoomSegmenter
{
    private DoorDetector $doorDetector;

    public function __construct(DoorDetector $doorDetector)
    {
        $this->doorDetector = $doorDetector;
    }

    /**
     * @return array<int, array<int, int|null>>
     */
    public function segment(Grid $grid): array
    {
        $height = $grid->getHeight();
        $roomMap = [];
        $visited = [];

        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            $roomMap[$r] = array_fill(0, $rowLen, null);
            $visited[$r] = array_fill(0, $rowLen, false);
        }

        $roomId = 0;

        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                if ($grid->isEmpty($r, $c) && !$visited[$r][$c] && !$this->doorDetector->isDoor($grid, $r, $c)) {
                    $roomId++;
                    $this->bfs($grid, $r, $c, $roomId, $visited, $roomMap);
                }
            }
        }

        return $roomMap;
    }

    /**
     * @param array<int, array<int, bool>> $visited
     * @param array<int, array<int, int|null>> $roomMap
     */
    private function bfs(
        Grid $grid,
        int $startR,
        int $startC,
        int $roomId,
        array &$visited,
        array &$roomMap
    ): void {
        $queue = [[$startR, $startC]];
        $visited[$startR][$startC] = true;

        $directions = [
            [-1, 0],
            [1, 0],
            [0, -1],
            [0, 1]
        ];

        while (!empty($queue)) {
            [$currR, $currC] = array_shift($queue);
            $roomMap[$currR][$currC] = $roomId;

            foreach ($directions as [$dr, $dc]) {
                $nr = $currR + $dr;
                $nc = $currC + $dc;

                if ($grid->getCell($nr, $nc) !== null) {
                    if ($grid->isEmpty($nr, $nc) && !$visited[$nr][$nc] && !$this->doorDetector->isDoor($grid, $nr, $nc)) {
                        $visited[$nr][$nc] = true;
                        $queue[] = [$nr, $nc];
                    }
                }
            }
        }
    }
}
