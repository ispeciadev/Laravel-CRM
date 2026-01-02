---
description: Deploy Laravel CRM to production server
---

# Laravel CRM Deployment Workflow

This workflow guides you through deploying the Laravel CRM application to a production server.

## Prerequisites Check

Before starting deployment, ensure you have:

-   Server access (SSH credentials)
-   Domain name configured and pointing to your server
-   Database credentials ready
-   SMTP credentials for email functionality
-   (Optional) Twilio credentials for VoIP features

## Standard Deployment Steps

### 1. Prepare Your Server

**Install Required Software:**

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1 and required extensions
sudo apt install -y php8.1 php8.1-fpm php8.1-cli php8.1-common php8.1-mysql \
  php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath \
  php8.1-intl php8.1-tokenizer php8.1-dom php8.1-fileinfo

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (LTS)
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor for queue workers
sudo apt install -y supervisor

# Install Nginx
sudo apt install -y nginx
```

### 2. Setup Database

```bash
# Login to MySQL
sudo mysql

# Create database and user (run these SQL commands)
CREATE DATABASE laravel_crm;
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON laravel_crm.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Deploy Application Code

```bash
# Navigate to web directory
cd /var/www

# Clone or upload your application
# Option A: If using Git
sudo git clone <your-repository-url> laravel-crm
# Option B: Upload via SCP/SFTP to /var/www/laravel-crm

# Set ownership
sudo chown -R www-data:www-data /var/www/laravel-crm

# Navigate to app directory
cd /var/www/laravel-crm

# Install PHP dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
sudo -u www-data npm install
sudo -u www-data npm run build
```

### 4. Configure Environment

```bash
# Copy environment file
sudo -u www-data cp .env.example .env

# Edit .env file with your settings
sudo nano .env
```

**Critical .env Settings:**

```ini
APP_NAME="Your CRM Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://crm.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_crm
DB_USERNAME=crm_user
DB_PASSWORD=your_secure_password

# Email Configuration (example with SendGrid)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 5. Initialize Application

```bash
# Generate application key
sudo -u www-data php artisan key:generate

# Run migrations and seeders
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force

# Create storage symlink
sudo -u www-data php artisan storage:link

# Clear and cache configuration
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 6. Configure Nginx

```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/laravel-crm
```

**Nginx Configuration:**

```nginx
server {
    listen 80;
    server_name crm.yourdomain.com;
    root /var/www/laravel-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/laravel-crm /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 7. Setup SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d crm.yourdomain.com

# Auto-renewal is configured automatically
```

### 8. Configure Queue Workers (Supervisor)

```bash
# Create supervisor configuration
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

**Supervisor Configuration:**

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-crm/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laravel-crm/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*

# Check status
sudo supervisorctl status
```

### 9. Setup Cron for Scheduled Tasks

```bash
# Edit crontab for www-data user
sudo crontab -u www-data -e

# Add this line:
* * * * * cd /var/www/laravel-crm && php artisan schedule:run >> /dev/null 2>&1
```

### 10. Verify Deployment

```bash
# Check application status
curl https://crm.yourdomain.com

# Check queue workers
sudo supervisorctl status laravel-worker:*

# Check logs
tail -f /var/www/laravel-crm/storage/logs/laravel.log
```

**Access Admin Panel:**

-   URL: `https://crm.yourdomain.com/admin/login`
-   Email: `admin@example.com`
-   Password: `admin123`

**⚠️ IMPORTANT: Change the default admin password immediately!**

---

## Custom Server Deployment (VPS/Dedicated Server)

If you're deploying to a custom server (DigitalOcean, Linode, AWS EC2, etc.), follow these additional considerations:

### Firewall Configuration

```bash
# Configure UFW firewall
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable

# Check status
sudo ufw status
```

### Server Optimization

**PHP-FPM Optimization:**

```bash
# Edit PHP-FPM pool configuration
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

Adjust these settings based on your server resources:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

```bash
# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

**MySQL Optimization:**

```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add/modify:

```ini
[mysqld]
max_connections = 100
innodb_buffer_pool_size = 1G  # 70% of available RAM
innodb_log_file_size = 256M
query_cache_size = 0
query_cache_type = 0
```

```bash
# Restart MySQL
sudo systemctl restart mysql
```

### Monitoring Setup

```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Check system resources
htop

# Monitor MySQL
sudo mysqladmin -u root -p status
sudo mysqladmin -u root -p processlist

# Monitor Nginx
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
```

### Backup Strategy

```bash
# Create backup script
sudo nano /usr/local/bin/backup-crm.sh
```

**Backup Script:**

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/laravel-crm"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/var/www/laravel-crm"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u crm_user -p'your_password' laravel_crm > $BACKUP_DIR/db_$DATE.sql

# Backup application files
tar -czf $BACKUP_DIR/app_$DATE.tar.gz $APP_DIR/storage $APP_DIR/.env

# Keep only last 7 days of backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-crm.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-crm.sh
```

### VoIP Configuration for Custom Servers

If using VoIP features:

1. **Ensure Public IP Access:**

    - Your server must have a public IP address
    - Firewall must allow incoming webhooks from Twilio

2. **Configure Twilio Webhook:**

    - In Twilio Console, set Voice Webhook URL to: `https://crm.yourdomain.com/api/voip/webhook`
    - Method: POST

3. **Whitelist Twilio IPs (if using firewall):**
    ```bash
    # Add Twilio IP ranges to firewall
    # Check current ranges at: https://www.twilio.com/docs/ips
    ```

### Performance Tuning

**Enable OPcache:**

```bash
# Edit PHP configuration
sudo nano /etc/php/8.1/fpm/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

**Redis for Caching (Optional but Recommended):**

```bash
# Install Redis
sudo apt install -y redis-server php8.1-redis

# Start Redis
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

Update `.env`:

```ini
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

```bash
# Restart queue workers after changing .env
sudo supervisorctl restart laravel-worker:*
```

---

## Post-Deployment Checklist

-   [ ] Application accessible via HTTPS
-   [ ] Admin login working
-   [ ] Email sending functional (test from CRM)
-   [ ] Email tracking pixel working (check sent emails)
-   [ ] Queue workers running (`sudo supervisorctl status`)
-   [ ] Cron jobs configured
-   [ ] Default admin password changed
-   [ ] Backups configured and tested
-   [ ] Firewall configured
-   [ ] SSL certificate auto-renewal enabled
-   [ ] VoIP configured (if using)
-   [ ] Monitoring tools installed
-   [ ] Error logs accessible and monitored

---

## Troubleshooting

**Queue not processing:**

```bash
# Restart queue workers
php artisan queue:restart
sudo supervisorctl restart laravel-worker:*
```

**Permission errors:**

```bash
sudo chown -R www-data:www-data /var/www/laravel-crm
sudo chmod -R 775 storage bootstrap/cache
```

**Email tracking not working:**

-   Verify `APP_URL` in `.env` matches your domain exactly
-   Restart queue workers after changing `.env`
-   Check email HTML source for tracking pixel URL

**500 errors:**

```bash
# Check logs
tail -f /var/www/laravel-crm/storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**VoIP webhooks failing:**

-   Ensure server is publicly accessible
-   Check firewall allows incoming connections
-   Verify webhook URL in Twilio console
-   Check `/var/www/laravel-crm/storage/logs/laravel.log` for errors

---

## Maintenance Commands

```bash
# Update application
cd /var/www/laravel-crm
sudo -u www-data git pull
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install && npm run build
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
php artisan queue:restart

# View logs
tail -f storage/logs/laravel.log

# Monitor queue
php artisan queue:monitor

# Clear all cache
php artisan optimize:clear
```
