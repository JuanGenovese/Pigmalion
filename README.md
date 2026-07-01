# Colored Rooms CLI Solver

An elegant PHP CLI utility that validates, segments, and paints ASCII-art architectural floor plans with distinct ANSI colors for each room, keeping doors and exterior transitions transparent.

## Features

- **Character Validation**: Ensures only wall characters (`#`) and empty spaces (` `) are parsed.
- **Door Width & Adjacency Constraints**: Enforces that door gaps (horizontal or vertical) do not exceed a width/height of 1 cell, preventing contiguous or oversized door corridors.
- **Closed Perimeter Validation**: Confirms that room interiors do not leak to out-of-bounds cells, enforcing airtight building perimeters.
- **Room Segmentation**: Labels connected room spaces (using 4-directional flood-fill/BFS) into distinct rooms while ignoring doors and walls.
- **ANSI Console Renderer**: Renders the final layout in the terminal with premium background colors for room cells while keeping walls and doors transparent.

---

## Architecture & Class Design

The project is structured under `src/` following a clean, decoupled service architecture:

```
src/
├── Exception/                 # Domain validation exceptions
│   ├── ContiguousDoorsException.php
│   ├── InvalidCharacterException.php
│   └── OpenPerimeterException.php
├── Model/
│   └── Grid.php               # Grid model representing ASCII coordinates & boundaries
└── Service/
    ├── DoorDetector.php       # Detects structural doors based on wall flanking count
    ├── Validator.php          # Validates character set, perimeter integrity, and door rules
    ├── RoomSegmenter.php      # BFS-based connected component room segmentation
    └── AnsiRenderer.php       # Colorizes and renders the segmented grid to terminal
```

---

## Installation & Setup

All dependencies and runtime environments are fully containerized using Docker.

1. **Build and install dependencies**:
   ```bash
   docker compose run --rm php composer install
   ```

---

## Usage

You can run the colored room solver using the provided Bash wrapper `./colorear.sh`.

### 1. From a File
Pass the floor plan file path as the first argument:
```bash
./colorear.sh samples/map1.txt
```

### 2. From Standard Input (Stdin)
Pipe the floor plan directly into the script:
```bash
cat samples/map1.txt | ./colorear.sh
```

---

## Testing

The project is fully covered by unit tests validating the model, door detection rules, specific validation constraints, and the BFS segmentation.

Run all tests inside the PHP container:
```bash
docker compose run --rm php vendor/bin/phpunit tests
```
