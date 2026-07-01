<?php

declare(strict_types=1);

namespace Tests\Model;

use App\Model\Grid;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    public function testConstructAndBasicGetters(): void
    {
        $input = "###\n# #\n###";
        $grid = new Grid($input);

        $this->assertEquals(3, $grid->getHeight());
        $this->assertEquals(3, $grid->getRowLength(0));
        $this->assertEquals(3, $grid->getRowLength(1));

        $this->assertEquals('#', $grid->getCell(0, 0));
        $this->assertEquals(' ', $grid->getCell(1, 1));
        $this->assertNull($grid->getCell(3, 0));
        $this->assertNull($grid->getCell(1, 5));
    }

    public function testCellChecks(): void
    {
        $input = "###\n# #\n###";
        $grid = new Grid($input);

        $this->assertTrue($grid->isWall(0, 0));
        $this->assertFalse($grid->isWall(1, 1));
        $this->assertFalse($grid->isWall(3, 0)); // Out of bounds is not wall

        $this->assertTrue($grid->isEmpty(1, 1));
        $this->assertFalse($grid->isEmpty(0, 0));
        $this->assertFalse($grid->isEmpty(3, 0)); // Out of bounds is not empty
    }

    public function testIrregularRowLengths(): void
    {
        $input = "####\n#\n###";
        $grid = new Grid($input);

        $this->assertEquals(3, $grid->getHeight());
        $this->assertEquals(4, $grid->getRowLength(0));
        $this->assertEquals(1, $grid->getRowLength(1));
        $this->assertEquals(3, $grid->getRowLength(2));
    }
}
