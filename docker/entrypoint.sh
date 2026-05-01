#!/bin/bash

# Wait for database to be ready when host/port are provided explicitly.
DB_WAIT_HOST="${DB_HOST:-}"
DB_WAIT_PORT="${DB_PORT:-}"

if [ -n "$DB_WAIT_HOST" ] && [ -n "$DB_WAIT_PORT" ]; then
  echo "Waiting for database at $DB_WAIT_HOST:$DB_WAIT_PORT..."
  for i in {1..30}; do
    if nc -z "$DB_WAIT_HOST" "$DB_WAIT_PORT" 2>/dev/null; then
      echo "✓ Database ready!"
      break
    fi
    sleep 1
  done
else
  echo "Skipping database wait: DB_HOST or DB_PORT is not set."
fi

echo "Starting services..."

# Prefer APP_KEY from the mounted .env file when it is not provided explicitly.
if [ -z "$APP_KEY" ] && [ -f /app/.env ]; then
  APP_KEY_FROM_ENV=$(grep -E '^APP_KEY=' /app/.env | head -n 1 | cut -d '=' -f 2-)
  if [ -n "$APP_KEY_FROM_ENV" ]; then
    export APP_KEY="$APP_KEY_FROM_ENV"
  fi
fi

# Start supervisord
exec "$@"
