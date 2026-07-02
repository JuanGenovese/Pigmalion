<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Model\Grid;
use App\Service\DoorDetector;
use App\Service\Validator;
use App\Exception\InvalidCharacterException;
use App\Exception\ContiguousDoorsException;
use App\Exception\OpenPerimeterException;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $doorDetector = new DoorDetector();
        $this->validator = new Validator($doorDetector);
    }

    public function testValidGridPasses(): void
    {
        $input = "##########\n#   #    #\n#   #    #\n## #### ##\n#        #\n#        #\n##########";
        $grid = new Grid($input);

        $this->validator->validate($grid);
        $this->assertTrue(true); // Assert no exception was thrown
    }

    public function testInvalidCharacterThrowsException(): void
    {
        $input = "##########\n#   A    #\n##########";
        $grid = new Grid($input);

        $this->expectException(InvalidCharacterException::class);
        $this->validator->validate($grid);
    }

    public function testContiguousDoorsNoLongerThrowsException(): void
    {
        // Two horizontal spaces next to each other flanked by walls (door of width 2)
        $input = "##########\n#        #\n##  ######\n#        #\n##########";
        $grid = new Grid($input);

        $this->validator->validate($grid);
        $this->assertTrue(true); // Should pass without exception
    }

    public function testOpenPerimeterThrowsExceptionBoundarySpace(): void
    {
        // Right side of Row 1 has a space instead of a wall, opening the perimeter
        $input = "##########\n#   #     \n#   #    #\n## #### ##\n#        #\n#        #\n##########";
        $grid = new Grid($input);

        $this->expectException(OpenPerimeterException::class);
        $this->validator->validate($grid);
    }

    public function testOpenPerimeterThrowsExceptionIrregularRow(): void
    {
        // Row 2 is too short, leaving the space at (1,3) open to the bottom outside
        $input = "#####\n#   #\n###";
        $grid = new Grid($input);

        $this->expectException(OpenPerimeterException::class);
        $this->validator->validate($grid);
    }
}
