<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Grid;
use App\Service\DoorDetector;
use App\Service\Validator;
use App\Service\RoomSegmenter;
use App\Service\AnsiRenderer;
use App\Exception\InvalidCharacterException;
use App\Exception\ContiguousDoorsException;
use App\Exception\OpenPerimeterException;

// 1. Read input
$input = '';
if (isset($argv[1])) {
    $filePath = $argv[1];
    if (!file_exists($filePath)) {
        fwrite(STDERR, "Error: File '{$filePath}' not found.\n");
        exit(1);
    }
    $input = file_get_contents($filePath);
} else {
    // Fallback to stdin
    $input = file_get_contents('php://stdin');
}

if ($input === false || trim($input) === '') {
    fwrite(STDERR, "Error: Empty input floor plan.\n");
    exit(1);
}

try {
    // 2. Parse grid
    $grid = new Grid($input);

    // 3. Initialize services
    $doorDetector = new DoorDetector();
    $validator = new Validator($doorDetector);
    $segmenter = new RoomSegmenter($doorDetector);
    $renderer = new AnsiRenderer();

    // 4. Validate
    $validator->validate($grid);

    // 5. Segment and render
    $roomMap = $segmenter->segment($grid);
    $output = $renderer->render($grid, $roomMap);

    // 6. Print result
    echo $output . "\n";
    exit(0);

} catch (InvalidCharacterException $e) {
    fwrite(STDERR, "Validation Error (Invalid Character): " . $e->getMessage() . "\n");
    exit(2);
} catch (ContiguousDoorsException $e) {
    fwrite(STDERR, "Validation Error (Contiguous Doors / Invalid Door Width): " . $e->getMessage() . "\n");
    exit(3);
} catch (OpenPerimeterException $e) {
    fwrite(STDERR, "Validation Error (Open Perimeter): " . $e->getMessage() . "\n");
    exit(4);
} catch (\Throwable $e) {
    fwrite(STDERR, "Unexpected Error: " . $e->getMessage() . "\n");
    exit(1);
}
