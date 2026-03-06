#!/usr/bin/env bash
set -euo pipefail

COMPOSE=(docker compose -f docker-compose.yml -f docker-compose.dev.yml)

case "${1:-up}" in
  up)
    "${COMPOSE[@]}" up -d --build
    ;;
  down)
    "${COMPOSE[@]}" down
    ;;
  logs)
    "${COMPOSE[@]}" logs -f
    ;;
  artisan)
    shift
    "${COMPOSE[@]}" exec app php artisan "$@"
    ;;
  npm)
    shift
    "${COMPOSE[@]}" exec vite npm "$@"
    ;;
  *)
    echo "Usage: $0 [up|down|logs|artisan|npm]"
    exit 1
    ;;
esac
