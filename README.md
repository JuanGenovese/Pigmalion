# Pigmalion Exámen Técnico SSR
---

## Características

- **Validación de Caracteres**: Asegura que solo se procesen caracteres de pared (`#`) y espacios vacíos (` `).
- **Restricciones de Ancho y Adyacencia de Puertas**: Garantiza que las aberturas de las puertas (horizontales o verticales) no excedan el ancho/alto de 1 celda, evitando pasillos de puertas contiguos o de gran tamaño.
- **Validación de Perímetro Cerrado**: Confirma que el interior de las habitaciones no se filtre hacia celdas fuera de los límites, garantizando perímetros cerrados.
- **Segmentación de Habitaciones**: Etiqueta los espacios conectados en habitaciones distintas, ignorando puertas y paredes.
- **Renderizador de Consola ANSI**: Renderiza el diseño final en la terminal con colores de fondo para las celdas de las habitaciones, mientras mantiene transparentes las paredes y las puertas.

---

## Arquitectura y Diseño de Clases

El proyecto está estructurado dentro de `src/` siguiendo una arquitectura de servicios desacoplados:

```
src/
├── Exception/                 # Excepciones de validación del dominio
│   ├── ContiguousDoorsException.php
│   ├── InvalidCharacterException.php
│   └── OpenPerimeterException.php
├── Model/
│   └── Grid.php               # Modelo de grilla que representa las coordenadas y límites ASCII
└── Service/
    ├── DoorDetector.php       # Detecta puertas estructurales según los muros flanqueantes
    ├── Validator.php          # Valida el conjunto de caracteres, integridad del perímetro y reglas de puertas
    ├── RoomSegmenter.php      # Segmentación de habitaciones
    └── AnsiRenderer.php       # Colorea y renderiza la grilla segmentada en la terminal
```

---

## Instalación y Configuración

Todas las dependencias y entornos de ejecución están contenedorizados utilizando Docker.

1. **Construir e instalar dependencias**:
   ```bash
   docker compose run --rm php composer install
   ```

---

## Uso

### 1. Desde un Archivo
Pasá la ruta del plano como primer argumento:
```bash
./colorear.sh samples/map1.txt
```

### 2. Desde la Entrada Estándar (Stdin)
Mandá el plano directamente al script:
```bash
cat samples/map1.txt | ./colorear.sh
```

---

## Tests

El proyecto está cubierto por pruebas unitarias que validan el modelo, las reglas de detección de puertas, las restricciones específicas de validación y la segmentación de habitaciones.

Ejecutá todos los tests dentro del contenedor de PHP:
```bash
docker compose run --rm php vendor/bin/phpunit tests
```
