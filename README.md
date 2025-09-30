# Survey Kepuasan Layanan - Pusbin JF MKG

Aplikasi web untuk survey kepuasan layanan menggunakan Laravel Framework.

## 🚀 Fitur Utama

### Untuk Admin
- **Dashboard** - Statistik dan ringkasan data survey
- **Manajemen Survey** - Buat, edit, hapus survey
- **Manajemen Kategori** - Kelola kategori pertanyaan
- **Manajemen Pertanyaan** - Kelola bank pertanyaan
- **Analisis Survey** - Analisis hasil dengan grafik dan chart
- **Export Data** - Download PDF, CSV, Excel
- **Backup Database** - Backup otomatis dan manual
- **Audit Log** - Log aktivitas keamanan

### Untuk Responden
- **Isi Survey** - Form survey yang user-friendly
- **Panduan Pengisian** - Petunjuk lengkap
- **Responsive Design** - Support mobile dan desktop

## 🛠️ Teknologi yang Digunakan

- **Backend:** Laravel 9.x
- **Frontend:** TailwindCSS, Alpine.js, Chart.js
- **Database:** MySQL 8.0+
- **PHP:** 8.0+
- **Server:** Nginx + PHP-FPM

## 📋 Persyaratan Sistem

- PHP 8.0 atau lebih tinggi
- MySQL 8.0 atau MariaDB 10.6+
- Composer
- Node.js & NPM
- Web Server (Nginx/Apache)

## 🔧 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/survey-kepuasan.git
cd survey-kepuasan
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=survey_kepuasan
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Migrasi Database
```bash
php artisan migrate --seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Jalankan Aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👤 Akun Default

**Admin:**
- Email: `pegawai@bmkg.go.id`
- Password: `pegawai123`

## 🔒 Keamanan

Aplikasi ini dilengkapi dengan fitur keamanan:
- Session encryption
- Rate limiting
- Security headers
- Audit logging
- CSRF protection
- Input validation

## 📊 Fitur Analisis

- **Gap Analysis** - Perbandingan kepuasan vs kepentingan
- **Distribusi Data** - Chart distribusi kepuasan dan kepentingan
- **Profil Demografis** - Analisis berdasarkan demografi
- **Hasil Akhir** - Kategorisasi A/B/C/D
- **Export Laporan** - PDF, CSV, Excel

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Konfigurasi SSL/HTTPS
- [ ] Setup backup otomatis
- [ ] Konfigurasi monitoring
- [ ] Setup firewall

### Server Requirements
- **CPU:** 2 vCPU minimum
- **RAM:** 4GB minimum
- **Storage:** 50GB SSD
- **OS:** Ubuntu 20.04+ / CentOS 8+

## 📝 Dokumentasi

- [Deployment Guide](deployment-guides/README.md)
- [Security Configuration](SECURITY.md)
- [API Documentation](docs/api.md)

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Kontak

- **Developer:** [Your Name]
- **Email:** [your.email@domain.com]
- **Project Link:** [https://github.com/yourusername/survey-kepuasan](https://github.com/yourusername/survey-kepuasan)

## 🙏 Acknowledgments

- Laravel Framework
- TailwindCSS
- Chart.js
- Pusbin JF MKG Team