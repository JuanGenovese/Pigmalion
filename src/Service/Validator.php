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

        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                $char = $grid->getCell($r, $c);
                if ($char !== '#' && $char !== ' ') {
                    throw new InvalidCharacterException("Caracter invalido '{$char}' en (fila {$r}, columna {$c}). Solo se permiten '#' y ' '");
                }
            }
        }

        for ($r = 0; $r < $height; $r++) {
            $rowLen = $grid->getRowLength($r);
            for ($c = 0; $c < $rowLen; $c++) {
                if ($grid->isEmpty($r, $c)) {
                    if (!$this->doorDetector->isDoor($grid, $r, $c)) {
                        $neighbors = [
                            [$r - 1, $c],
                            [$r + 1, $c],
                            [$r, $c - 1],
                            [$r, $c + 1]
                        ];
                        foreach ($neighbors as [$nr, $nc]) {
                            if ($grid->getCell($nr, $nc) === null) {
                                throw new OpenPerimeterException("Perímetro abierto detectado: espacio de habitacion en (fila {$r}, columna {$c}) está expuesto al exterior.");
                            }
                        }
                    }
                }
            }
        }
    }
}
