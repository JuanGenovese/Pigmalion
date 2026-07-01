<?php

declare(strict_types=1);

namespace App\Model;

class Grid
{
    /** @var list<string> */
    private array $rows;

    private int $height;

    public function __construct(string $input)
    {
        // Split by newline and normalize
        $lines = preg_split('/\r\n|\r|\n/', $input);
        if ($lines === false) {
            $lines = [];
        }

        // Remove trailing empty line if input ended with a newline
        if (!empty($lines) && end($lines) === '') {
            array_pop($lines);
        }

        $this->rows = array_values($lines);
        $this->height = count($this->rows);
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getRowLength(int $r): int
    {
        if ($r < 0 || $r >= $this->height) {
            return 0;
        }
        return strlen($this->rows[$r]);
    }

    public function getCell(int $r, int $c): ?string
    {
        if ($r < 0 || $r >= $this->height) {
            return null;
        }
        $row = $this->rows[$r];
        if ($c < 0 || $c >= strlen($row)) {
            return null;
        }
        return $row[$c];
    }

    public function isWall(int $r, int $c): bool
    {
        return $this->getCell($r, $c) === '#';
    }

    public function isEmpty(int $r, int $c): bool
    {
        return $this->getCell($r, $c) === ' ';
    }

    /**
     * @return list<string>
     */
    public function getRows(): array
    {
        return $this->rows;
    }
}
