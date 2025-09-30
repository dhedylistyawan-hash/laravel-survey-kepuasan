# 🛡️ Sistem Backup Otomatis - Survey Kepuasan BMKG

## 📋 **Status Implementasi**

✅ **SELESAI** - Sistem backup otomatis telah diimplementasi dengan lengkap!

## 🚀 **Fitur Backup yang Tersedia**

### 1. **Backup Otomatis Harian**
- **Jadwal**: Setiap hari jam 02:00 WIB
- **Lokasi**: `storage/backups/`
- **Format**: `survey_kepuasan_YYYYMMDD_HHMMSS.sql.gz`
- **Retensi**: 7 hari (otomatis dihapus)

### 2. **Cleanup Otomatis**
- **Jadwal**: Setiap minggu hari Minggu jam 03:00 WIB
- **Fungsi**: Hapus backup lebih dari 7 hari
- **Manfaat**: Hemat ruang penyimpanan

### 3. **Backup Manual**
- **Command**: `php artisan backup:database`
- **Script**: `bash backup-database.sh`
- **Opsi**: `--compress` untuk kompresi

## 🔧 **Command yang Tersedia**

### **Backup Database**
```bash
# Backup dengan kompresi
php artisan backup:database --compress

# Backup tanpa kompresi
php artisan backup:database

# Menggunakan script bash
bash backup-database.sh
```

### **Status Backup**
```bash
# Lihat status semua backup
php artisan backup:status
```

### **Cleanup Manual**
```bash
# Hapus backup lebih dari 7 hari
php artisan backup:cleanup

# Hapus backup lebih dari 30 hari
php artisan backup:cleanup --days=30
```

## ⚙️ **Setup Cron Job**

### **1. Setup Otomatis**
```bash
bash setup-backup-cron.sh
```

### **2. Setup Manual**
```bash
# Edit crontab
crontab -e

# Tambahkan baris ini:
* * * * * cd /path/to/survey-kepuasan && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 **Monitoring Backup**

### **Cek Status**
```bash
php artisan backup:status
```

**Output contoh:**
```
📊 Status Backup Database Survey Kepuasan
=====================================
+----------------------------------+------------------+--------+---------+--------+
| File                             | Tanggal          | Ukuran | Usia    | Status |
+----------------------------------+------------------+--------+---------+--------+
| survey_kepuasan_20241201_020001.sql.gz | 01/12/2024 02:00:01 | 2.5 MB  | 0 hari  | ✅ Baru |
| survey_kepuasan_20241130_020001.sql.gz | 30/11/2024 02:00:01 | 2.3 MB  | 1 hari  | ✅ Baru |
+----------------------------------+------------------+--------+---------+--------+

📈 Total file backup: 2
💾 Total ukuran: 4.8 MB
🕒 Backup terbaru: 01/12/2024 02:00:01
```

## 📁 **Struktur File Backup**

```
storage/
└── backups/
    ├── survey_kepuasan_20241201_020001.sql.gz
    ├── survey_kepuasan_20241130_020001.sql.gz
    └── survey_kepuasan_20241129_020001.sql.gz
```

## 🔄 **Restore Database**

### **Dari File .sql**
```bash
# Restore database
mysql -u username -p database_name < backup_file.sql
```

### **Dari File .sql.gz**
```bash
# Restore database terkompresi
gunzip -c backup_file.sql.gz | mysql -u username -p database_name
```

## 🛡️ **Keamanan Backup**

### **1. Enkripsi (Opsional)**
```bash
# Enkripsi backup
gpg --symmetric --cipher-algo AES256 survey_kepuasan_20241201_020001.sql.gz
```

### **2. Backup ke Cloud**
```bash
# Upload ke AWS S3
aws s3 cp survey_kepuasan_20241201_020001.sql.gz s3://backup-bucket/

# Upload ke Google Drive
rclone copy survey_kepuasan_20241201_020001.sql.gz gdrive:backups/
```

## 📈 **Monitoring & Alert**

### **1. Log Monitoring**
```bash
# Cek log Laravel
tail -f storage/logs/laravel.log

# Cek log cron
tail -f /var/log/cron.log
```

### **2. Email Notification (Opsional)**
Tambahkan di `BackupDatabaseCommand.php`:
```php
// Kirim email setelah backup
Mail::to('admin@bmkg.go.id')->send(new BackupCompletedMail($backupFile));
```

## 🚨 **Troubleshooting**

### **1. Backup Gagal**
```bash
# Cek koneksi database
php artisan tinker
>>> DB::connection()->getPdo();

# Cek permission folder
ls -la storage/backups/
```

### **2. Cron Tidak Berjalan**
```bash
# Cek status cron
systemctl status cron

# Restart cron
sudo systemctl restart cron

# Test manual
php artisan schedule:run
```

### **3. Ruang Disk Penuh**
```bash
# Cek penggunaan disk
df -h

# Hapus backup lama manual
php artisan backup:cleanup --days=3
```

## 📋 **Checklist Deployment**

- [ ] Setup cron job dengan `bash setup-backup-cron.sh`
- [ ] Test backup manual dengan `php artisan backup:database`
- [ ] Cek status dengan `php artisan backup:status`
- [ ] Verifikasi backup otomatis berjalan
- [ ] Setup monitoring dan alert (opsional)
- [ ] Test restore database (opsional)

## 🎯 **Keuntungan Sistem Ini**

1. **Otomatis** - Tidak perlu intervensi manual
2. **Efisien** - Kompresi dan cleanup otomatis
3. **Reliable** - Error handling dan logging
4. **Monitoring** - Status dan informasi detail
5. **Fleksibel** - Bisa manual atau otomatis
6. **Aman** - Backup terpisah dari aplikasi

---

**Sistem backup otomatis siap digunakan! 🚀**
