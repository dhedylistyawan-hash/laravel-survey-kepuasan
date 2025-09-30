# Deployment Checklist - Survey Kepuasan

## ✅ **PRE-DEPLOYMENT CHECKS**

### 1. **Database & Migration**
- [ ] Semua migration sudah dijalankan: `php artisan migrate`
- [ ] Database connection sudah benar
- [ ] Field baru sudah ada: `importance`, `satisfaction`, `suggestion`, `overall_rating`
- [ ] Seeder sudah dijalankan: `php artisan db:seed`

### 2. **Dependencies**
- [ ] Composer dependencies: `composer install --optimize-autoloader --no-dev`
- [ ] NPM dependencies: `npm install`
- [ ] Build assets: `npm run build`
- [ ] Chart.js sudah terinstall

### 3. **Configuration**
- [ ] `.env` file sudah dikonfigurasi dengan benar
- [ ] `APP_KEY` sudah di-generate: `php artisan key:generate`
- [ ] Database credentials sudah benar
- [ ] Cache sudah di-clear: `php artisan config:clear`

### 4. **File Permissions**
- [ ] `storage/` directory writable
- [ ] `bootstrap/cache/` directory writable
- [ ] `public/` directory accessible

### 5. **Core Features Test**
- [ ] **Authentication**: Login/logout admin berfungsi
- [ ] **Survey Form**: Form survey bisa diisi dan disubmit
- [ ] **Dashboard**: Dashboard menampilkan data dengan benar
- [ ] **Analisis Survey**: Fitur analisis berfungsi
- [ ] **Export**: Export CSV dan PDF berfungsi
- [ ] **Admin Panel**: CRUD kategori, pertanyaan, survey berfungsi

### 6. **Data Validation**
- [ ] Dashboard menampilkan data satisfaction dan importance
- [ ] Gap analysis berfungsi dengan benar
- [ ] Chart menampilkan data dengan benar
- [ ] Export data sesuai dengan struktur baru

### 7. **Security**
- [ ] Admin middleware berfungsi
- [ ] CSRF protection aktif
- [ ] Input validation berfungsi
- [ ] File upload security (jika ada)

### 8. **Performance**
- [ ] Cache sudah dioptimasi
- [ ] Database queries sudah dioptimasi
- [ ] Assets sudah di-compress

## 🚀 **DEPLOYMENT STEPS**

### 1. **Server Preparation**
```bash
# Update server
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install nginx mysql-server php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip unzip git -y
```

### 2. **Application Deployment**
```bash
# Clone repository
git clone [repository-url] /var/www/survey-kepuasan

# Set permissions
sudo chown -R www-data:www-data /var/www/survey-kepuasan
sudo chmod -R 755 /var/www/survey-kepuasan
sudo chmod -R 775 /var/www/survey-kepuasan/storage
sudo chmod -R 775 /var/www/survey-kepuasan/bootstrap/cache

# Install dependencies
cd /var/www/survey-kepuasan
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Environment setup
cp .env.example .env
php artisan key:generate
# Edit .env with correct database credentials

# Database setup
php artisan migrate
php artisan db:seed

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. **Nginx Configuration**
```bash
# Create nginx config
sudo nano /etc/nginx/sites-available/survey-kepuasan

# Enable site
sudo ln -s /etc/nginx/sites-available/survey-kepuasan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 4. **SSL Certificate (Optional)**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com
```

## 🔧 **POST-DEPLOYMENT CHECKS**

### 1. **Functionality Tests**
- [ ] Website accessible via domain
- [ ] Survey form loads correctly
- [ ] Admin login works
- [ ] Dashboard displays data
- [ ] Export functions work
- [ ] Mobile responsiveness

### 2. **Performance Tests**
- [ ] Page load times acceptable
- [ ] Database queries optimized
- [ ] Images and assets loading
- [ ] Chart rendering works

### 3. **Security Tests**
- [ ] HTTPS redirect working
- [ ] Admin access restricted
- [ ] Form validation working
- [ ] No sensitive data exposed

### 4. **Backup Setup**
- [ ] Database backup configured
- [ ] File backup configured
- [ ] Backup automation working

## 📋 **MAINTENANCE TASKS**

### Daily
- [ ] Check error logs
- [ ] Monitor disk space
- [ ] Verify backups

### Weekly
- [ ] Update dependencies
- [ ] Review security
- [ ] Performance monitoring

### Monthly
- [ ] Full system backup
- [ ] Security audit
- [ ] Performance optimization

## 🚨 **EMERGENCY CONTACTS**

- **Server Admin**: [Contact Info]
- **Database Admin**: [Contact Info]
- **Application Developer**: [Contact Info]

## 📝 **NOTES**

- Application uses Laravel 10.x
- Database: MySQL/MariaDB
- Web Server: Nginx
- PHP Version: 8.1+
- Node.js: Required for asset compilation
- Chart.js: Required for dashboard charts
- DomPDF: Required for PDF export

## 🔄 **UPDATE PROCEDURE**

```bash
# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
``` 
