#!/bin/bash

# Database Backup Script untuk Survey Kepuasan BMKG
# Jalankan: bash backup-database.sh

set -e

# Konfigurasi
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)

# Buat folder backup jika belum ada
mkdir -p backups

# Generate nama file backup dengan timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="backups/survey_kepuasan_${TIMESTAMP}.sql"

echo "🔄 Memulai backup database..."

# Backup database
if [ -z "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" > "$BACKUP_FILE"
else
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"
fi

# Kompres file backup
gzip "$BACKUP_FILE"
BACKUP_FILE="${BACKUP_FILE}.gz"

echo "✅ Backup berhasil: $BACKUP_FILE"

# Hapus backup lama (>7 hari)
find backups/ -name "survey_kepuasan_*.sql.gz" -mtime +7 -delete

echo "🧹 Backup lama (>7 hari) telah dihapus"

# Tampilkan info backup
BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
echo "📊 Ukuran backup: $BACKUP_SIZE"
echo "📁 Lokasi: $(pwd)/$BACKUP_FILE"

echo ""
echo "=== BACKUP SELESAI ==="
