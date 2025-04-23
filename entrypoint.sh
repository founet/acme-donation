#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
  echo "📦 vendor/ not found. Installing dependencies with Composer..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "✅ vendor/ already exists. Skipping Composer install."
fi

echo "🔄 Running migrations and seeders..."
php artisan migrate:fresh --seed --force

echo "🚀 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8008
