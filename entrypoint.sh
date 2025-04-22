#!/bin/bash

# 1. Vérifier si 'vendor/autoload.php' existe
if [ ! -f "vendor/autoload.php" ]; then
  echo "📦 vendor/ non trouvé. Installation des dépendances avec composer..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "✅ vendor/ déjà présent. Skip composer install."
fi

# 2. Lancer Laravel
echo "🚀 Démarrage du serveur Laravel..."
php artisan serve --host=0.0.0.0 --port=8000
