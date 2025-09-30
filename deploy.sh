#!/bin/bash

# Laravel Deployment Automation Script
# Jalankan: bash deploy.sh

set -e

# 1. Set .env ke production
if grep -q '^APP_ENV=' .env; then
    sed -i.bak 's/^APP_ENV=.*/APP_ENV=production/' .env
else
    echo 'APP_ENV=production' >> .env
fi
if grep -q '^APP_DEBUG=' .env; then
    sed -i.bak 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
else
    echo 'APP_DEBUG=false' >> .env
fi

echo "[1/8] .env sudah di-set ke production."

# 1.5. Cek PHP extensions
echo "[1.5/8] Mengecek PHP extensions..."
if php -m | grep -q "gd"; then
    echo "✅ GD Extension: OK"
else
    echo "❌ GD Extension: TIDAK DITEMUKAN"
    echo "⚠️  Install dengan: sudo apt install php-gd"
    echo "⚠️  Chart demografis akan fallback ke tabel"
fi

if php -m | grep -q "mysql"; then
    echo "✅ MySQL Extension: OK"
else
    echo "❌ MySQL Extension: TIDAK DITEMUKAN"
    exit 1
fi

# 2. Laravel optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "[2/8] Laravel sudah dioptimasi."

# 3. Set permissions
chmod -R 775 storage bootstrap/cache || echo "(Lewati chmod, mungkin di Windows)"
echo "[3/8] Permissions storage & bootstrap/cache sudah di-set."

# 4. Migrasi database
read -p "Lanjutkan migrasi database? (y/n): " MIG
if [[ "$MIG" =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo "[4/8] Migrasi database selesai."
else
    echo "[4/8] Migrasi database dilewati."
fi

# 5. Seeder (opsional)
read -p "Jalankan database seeder? (y/n): " SEED
if [[ "$SEED" =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    echo "[5/8] Seeder selesai."
else
    echo "[5/8] Seeder dilewati."
fi

# 6. Build asset frontend
if [ -f package.json ]; then
    if [ -f node_modules/.bin/vite ] || [ -f node_modules/.bin/webpack ]; then
        npm run build
    else
        npm install
        npm run build
    fi
    echo "[6/8] Asset frontend sudah dibuild."
else
    echo "[6/8] Tidak ada package.json, lewati build asset."
fi

# 7. Backup database (opsional)
read -p "Backup database sebelum deploy? (y/n): " BAK
if [[ "$BAK" =~ ^[Yy]$ ]]; then
    bash backup-database.sh
    echo "[7/8] Backup database selesai."
else
    echo "[7/8] Backup database dilewati."
fi

# 8. Test chart generation
echo "[8/8] Testing chart generation..."
if php -r "if (extension_loaded('gd')) { echo 'Chart generation: OK'; } else { echo 'Chart generation: FALLBACK TO TABLE'; }"; then
    echo "✅ Chart generation test selesai."
else
    echo "⚠️  Chart generation akan menggunakan fallback tabel."
fi

echo "\n=== DEPLOYMENT SELESAI ==="
echo "Cek aplikasi Anda di browser!"
