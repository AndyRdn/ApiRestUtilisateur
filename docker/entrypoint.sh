#!/bin/bash

until pg_isready -h db -p 5432 -U app; do
  echo "Waiting for database..."
  sleep 1
done

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"