#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

if [ "$1" == "" ]; then
    docker compose -f "$DIR/docker-compose.yml" run --rm -T php php bin/color-rooms.php
else
    if [ ! -f "$1" ]; then
        echo "Error: File '$1' not found." >&2
        exit 1
    fi
    cat "$1" | docker compose -f "$DIR/docker-compose.yml" run --rm -T php php bin/color-rooms.php
fi
