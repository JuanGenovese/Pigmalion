<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Model\Grid;
use App\Service\DoorDetector;
use App\Service\RoomSegmenter;
use PHPUnit\Framework\TestCase;

class RoomSegmenterTest extends TestCase
{
    private RoomSegmenter $segmenter;

    protected function setUp(): void
    {
        $doorDetector = new DoorDetector();
        $this->segmenter = new RoomSegmenter($doorDetector);
    }

    public function testSegmentRoomsInFirstMap(): void
    {
        $input = "##########\n#   #    #\n#   #    #\n## #### ##\n#        #\n#        #\n##########";
        $grid = new Grid($input);

        $roomMap = $this->segmenter->segment($grid);

        // Verify dimensions
        $this->assertCount(7, $roomMap);
        $this->assertCount(10, $roomMap[0]);

        // Doors (3,2) and (3,7) should have null room ID
        $this->assertNull($roomMap[3][2]);
        $this->assertNull($roomMap[3][7]);

        // Room 1 cells (top left) should all have the same room ID (let's say ID A)
        $id1 = $roomMap[1][1];
        $this->assertNotNull($id1);
        $this->assertEquals($id1, $roomMap[1][2]);
        $this->assertEquals($id1, $roomMap[1][3]);
        $this->assertEquals($id1, $roomMap[2][1]);
        $this->assertEquals($id1, $roomMap[2][2]);
        $this->assertEquals($id1, $roomMap[2][3]);

        // Room 2 cells (top right) should all have the same room ID (ID B)
        $id2 = $roomMap[1][5];
        $this->assertNotNull($id2);
        $this->assertNotEquals($id1, $id2);
        $this->assertEquals($id2, $roomMap[1][6]);
        $this->assertEquals($id2, $roomMap[1][7]);
        $this->assertEquals($id2, $roomMap[1][8]);
        $this->assertEquals($id2, $roomMap[2][5]);
        $this->assertEquals($id2, $roomMap[2][6]);
        $this->assertEquals($id2, $roomMap[2][7]);
        $this->assertEquals($id2, $roomMap[2][8]);

        // Room 3 cells (bottom) should all have the same room ID (ID C)
        $id3 = $roomMap[4][1];
        $this->assertNotNull($id3);
        $this->assertNotEquals($id1, $id3);
        $this->assertNotEquals($id2, $id3);
        for ($c = 1; $c <= 8; $c++) {
            $this->assertEquals($id3, $roomMap[4][$c]);
            $this->assertEquals($id3, $roomMap[5][$c]);
        }

        // Walls should have null room ID
        $this->assertNull($roomMap[0][0]);
        $this->assertNull($roomMap[3][0]);
        $this->assertNull($roomMap[3][1]);
        $this->assertNull($roomMap[3][3]);
        $this->assertNull($roomMap[3][4]);
        $this->assertNull($roomMap[3][5]);
        $this->assertNull($roomMap[3][6]);
        $this->assertNull($roomMap[3][8]);
        $this->assertNull($roomMap[3][9]);
    }
}
