# Panduan Deployment Survey Kepuasan BMKG JF/Pusbin

## Platform Deployment

### 1. Shared Hosting (cPanel, Plesk, dll)

#### Langkah-langkah:
1. **Upload Files**
   ```bash
   # Upload semua file ke public_html atau folder yang ditentukan
   # Pastikan struktur folder tetap sama
   ```

2. **Konfigurasi .env**
   ```env
   APP_NAME="Survey Kepuasan BMKG JF/Pusbin"
   APP_ENV=production
   APP_KEY=base64:your-generated-key
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_cpanel_db_name
   DB_USERNAME=your_cpanel_db_user
   DB_PASSWORD=your_cpanel_db_password
   ```

3. **Set Permissions**
   ```bash
   # Via File Manager atau SSH
   chmod 755 storage/
   chmod 755 bootstrap/cache/
   chmod 644 .env
   ```

4. **Run Commands via SSH atau Terminal**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### 2. VPS/Cloud Server (Ubuntu/Debian)

#### Prerequisites:
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1+
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath php8.1-intl php8.1-soap php8.1-opcache

# Install MySQL
sudo apt install mysql-server

# Install Nginx
sudo apt install nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (untuk build assets)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

#### Deployment Steps:
```bash
# Clone/Upload project
cd /var/www/
sudo git clone your-repository survey-kepuasan
cd survey-kepuasan

# Set permissions
sudo chown -R www-data:www-data /var/www/survey-kepuasan
sudo chmod -R 755 /var/www/survey-kepuasan

# Install dependencies
composer install --optimize-autoloader --no-dev

# Copy .env
cp .env.example .env
# Edit .env sesuai konfigurasi production

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force
php artisan db:seed --force

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets
npm install
npm run build
```

### 3. Cloud Platforms

#### Heroku
```bash
# Install Heroku CLI
# Login to Heroku
heroku login

# Create app
heroku create your-app-name

# Add MySQL addon
heroku addons:create jawsdb:kitefin

# Deploy
git push heroku main

# Run migrations
heroku run php artisan migrate --force
heroku run php artisan db:seed --force
```

#### DigitalOcean App Platform
1. Connect repository ke DigitalOcean
2. Set environment variables
3. Deploy otomatis

#### AWS Elastic Beanstalk
```bash
# Install EB CLI
pip install awsebcli

# Initialize EB
eb init

# Create environment
eb create production

# Deploy
eb deploy
```

### 4. Docker Deployment

#### Dockerfile
```dockerfile
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Change current user to www
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
```

#### docker-compose.yml
```yaml
version: '3'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: survey-kepuasan
    container_name: survey-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - survey-network

  webserver:
    image: nginx:alpine
    container_name: survey-nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d/:/etc/nginx/conf.d/
    networks:
      - survey-network

  db:
    image: mysql:8.0
    container_name: survey-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: survey_kepuasan
      MYSQL_ROOT_PASSWORD: your_mysql_root_password
      SERVICE_TAGS: dev
      SERVICE_NAME: mysql
    ports:
      - "3306:3306"
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - survey-network

networks:
  survey-network:
    driver: bridge
volumes:
  dbdata:
    driver: local
```

## Troubleshooting

### Common Issues:

1. **500 Internal Server Error**
   - Check storage permissions
   - Check APP_KEY in .env
   - Check database connection
   - Check Laravel logs: `tail -f storage/logs/laravel.log`

2. **404 Not Found**
   - Check web server configuration
   - Check .htaccess (Apache) atau Nginx config
   - Check file permissions

3. **Database Connection Error**
   - Verify database credentials
   - Check if database exists
   - Check if user has proper permissions

4. **Export Features Not Working**
   - Check PHP extensions: GD, ZIP, XML
   - Check file permissions for storage
   - Check memory limit in php.ini

5. **Email Not Working**
   - Check SMTP configuration
   - Check firewall settings
   - Test with mail testing tools

### Performance Tips:

1. **Enable OPcache**
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.interned_strings_buffer=8
   opcache.max_accelerated_files=4000
   ```

2. **Use Redis for Cache**
   ```env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

3. **Enable Gzip Compression**
   ```nginx
   gzip on;
   gzip_vary on;
   gzip_min_length 1024;
   gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
   ```

## Security Checklist

- [ ] Set APP_DEBUG=false
- [ ] Use HTTPS
- [ ] Set secure database passwords
- [ ] Configure firewall
- [ ] Regular backups
- [ ] Update dependencies regularly
- [ ] Monitor logs
- [ ] Use environment variables for sensitive data 
