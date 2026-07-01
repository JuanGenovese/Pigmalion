<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Model\Grid;
use App\Service\DoorDetector;
use PHPUnit\Framework\TestCase;

class DoorDetectorTest extends TestCase
{
    private DoorDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new DoorDetector();
    }

    public function testDetectsHorizontalDoor(): void
    {
        $input = "#####\n#   #\n## ##\n#   #\n#####";
        $grid = new Grid($input);
        // The space at (2,2) is flanked horizontally by walls (2,1) and (2,3).
        // Its top and bottom are spaces (1,2) and (3,2).
        $this->assertTrue($this->detector->isDoor($grid, 2, 2));
    }

    public function testDetectsVerticalDoor(): void
    {
        $input = "#####\n# # #\n#   #\n# # #\n#####";
        $grid = new Grid($input);
        // (2,2) is flanked vertically by (1,2) and (3,2) which are walls.
        // Its left and right are spaces (2,1) and (2,3).
        $this->assertTrue($this->detector->isDoor($grid, 2, 2));
    }

    public function testNotDoorInsideRoom(): void
    {
        $input = "#####\n#   #\n#####";
        $grid = new Grid($input);
        // (1,2) is a space, but flanked horizontally by spaces (1,1) and (1,3).
        // And flanked vertically by walls (0,2) and (2,2).
        // Wait, is it a door because of vertical flanking?
        // Ah! If it is flanked vertically by walls, is it a door?
        // E.g., in:
        // #####
        // #   #
        // #####
        // (1,2) has wall above and wall below. So it is flanked vertically by walls.
        // But it is inside the room! It is NOT a door.
        // So we must ensure that a door is a PASSAGE, meaning it divides two walls.
        // If it is flanked vertically by walls, but it's part of a room of width 3,
        // is it a door? No!
        // This is a crucial distinction!
        $this->assertFalse($this->detector->isDoor($grid, 1, 2));
    }
}
