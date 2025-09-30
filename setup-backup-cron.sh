#!/bin/bash

# Setup Cron Job untuk Backup Otomatis
# Jalankan: bash setup-backup-cron.sh

echo "🔄 Setup Cron Job untuk Backup Otomatis..."

# Cek apakah Laravel scheduler sudah ada di crontab
if crontab -l | grep -q "artisan schedule:run"; then
    echo "✅ Laravel scheduler sudah ada di crontab"
else
    echo "➕ Menambahkan Laravel scheduler ke crontab..."
    
    # Tambahkan Laravel scheduler ke crontab
    (crontab -l 2>/dev/null; echo "* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    echo "✅ Laravel scheduler berhasil ditambahkan"
fi

# Cek status cron service
if systemctl is-active --quiet cron; then
    echo "✅ Cron service berjalan"
else
    echo "⚠️ Cron service tidak berjalan, mencoba start..."
    sudo systemctl start cron
    sudo systemctl enable cron
fi

# Test backup command
echo "🧪 Testing backup command..."
php artisan backup:status

echo ""
echo "=== SETUP SELESAI ==="
echo "📋 Cron job yang ditambahkan:"
echo "   - Laravel Scheduler: * * * * * (setiap menit)"
echo "   - Backup Database: Setiap hari jam 02:00"
echo "   - Cleanup Backup: Setiap minggu hari Minggu jam 03:00"
echo ""
echo "🔧 Command yang tersedia:"
echo "   - php artisan backup:database"
echo "   - php artisan backup:status"
echo "   - php artisan backup:cleanup"
echo "   - bash backup-database.sh"
