#!/bin/bash

# Wait for database to be ready
echo "Waiting for MySQL..."
for i in {1..30}; do
  if nc -z "$DB_HOST" 3306 2>/dev/null; then
    echo "✓ Database ready!"
    break
  fi
  sleep 1
done

echo "Starting services..."

# Export APP_KEY if not set (needed before PHP loads any configuration)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:yourgeneratedkeyhere" ]; then
  export APP_KEY="base64:ABC123+ABC123+ABC123+ABC123+ABC="
fi

# Start supervisord
exec "$@"
