#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
  echo "📦 Installing dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "✅ Dependencies already installed."
fi

echo "⏳ Waiting for PostgreSQL to be ready..."

until nc -z -v -w30 db 5432
do
  echo "⛔ PostgreSQL is unavailable - sleeping"
  sleep 2
done

echo "✅ PostgreSQL is up - running migrations and seeders..."
php artisan migrate:fresh --seed --force

echo "🚀 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000
