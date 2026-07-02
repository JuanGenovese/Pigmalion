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

$input = '';
if (isset($argv[1])) {
    $filePath = $argv[1];
    if (!file_exists($filePath)) {
        fwrite(STDERR, "Error: El archivo '{$filePath}' no existe.\n");
        exit(1);
    }
    $input = file_get_contents($filePath);
} else {
    $input = file_get_contents('php://stdin');
}

if ($input === false || trim($input) === '') {
    fwrite(STDERR, "Error: Plano vacío.\n");
    exit(1);
}

try {
    $grid = new Grid($input);

    $doorDetector = new DoorDetector();
    $validator = new Validator($doorDetector);
    $segmenter = new RoomSegmenter($doorDetector);
    $renderer = new AnsiRenderer();

    $validator->validate($grid);

    $roomMap = $segmenter->segment($grid);
    $output = $renderer->render($grid, $roomMap);

    echo $output . "\n";
    exit(0);

} catch (InvalidCharacterException $e) {
    fwrite(STDERR, "Error de validación (carácter inválido): " . $e->getMessage() . "\n");
    exit(2);
} catch (ContiguousDoorsException $e) {
    fwrite(STDERR, "Error de validación (puertas contiguas / ancho inválido): " . $e->getMessage() . "\n");
    exit(3);
} catch (OpenPerimeterException $e) {
    fwrite(STDERR, "Error de validación (perímetro abierto): " . $e->getMessage() . "\n");
    exit(4);
} catch (\Throwable $e) {
    fwrite(STDERR, "Error inesperado: " . $e->getMessage() . "\n");
    exit(1);
}
