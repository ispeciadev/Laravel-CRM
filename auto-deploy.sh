#!/bin/bash

################################################################################
# AUTOMATED PRODUCTION DEPLOYMENT SCRIPT FOR LARAVEL CRM
# This script automates ALL phases of deployment to a remote server
################################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

print_header() {
    echo -e "\n${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  $1${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

print_success() { echo -e "${GREEN}✓${NC} $1"; }
print_warning() { echo -e "${YELLOW}⚠${NC} $1"; }
print_error() { echo -e "${RED}✗${NC} $1"; }
print_info() { echo -e "${BLUE}ℹ${NC} $1"; }

################################################################################
# CONFIGURATION - EDIT THESE VALUES
################################################################################

# Server Details (You'll get these after creating DigitalOcean droplet)
SERVER_IP="YOUR_SERVER_IP"              # e.g., 164.92.123.45
SERVER_USER="deployer"                   # Username on server
SSH_KEY_PATH="$HOME/.ssh/id_rsa"        # Path to your SSH key

# Domain Details
DOMAIN_NAME="your-domain.com"            # Your domain name
WWW_DOMAIN="www.your-domain.com"         # WWW version

# Database Configuration
DB_NAME="laravel_crm"
DB_USER="crm_user"
DB_PASSWORD="CHANGE_THIS_STRONG_PASSWORD"  # IMPORTANT: Change this!

# Email Configuration (for notifications)
ADMIN_EMAIL="your-email@example.com"

# Application Details
APP_NAME="Laravel CRM"
LOCAL_APP_PATH="/home/Abhi/Downloads/laravel-crm-2.1.5"
REMOTE_APP_PATH="/var/www/laravel-crm"

################################################################################
# PHASE 1: PREPARE LOCAL MACHINE
################################################################################

phase1_prepare_local() {
    print_header "PHASE 1: PREPARING LOCAL MACHINE"
    
    # Create deployment package
    print_info "Creating deployment package..."
    cd "$(dirname "$LOCAL_APP_PATH")"
    tar -czf laravel-crm-deploy.tar.gz "$(basename "$LOCAL_APP_PATH")" \
        --exclude=node_modules \
        --exclude=vendor \
        --exclude=storage/logs/* \
        --exclude=storage/framework/cache/* \
        --exclude=storage/framework/sessions/* \
        --exclude=storage/framework/views/*
    
    print_success "Deployment package created: laravel-crm-deploy.tar.gz"
    
    # Generate SSH key if not exists
    if [ ! -f "$SSH_KEY_PATH" ]; then
        print_info "Generating SSH key..."
        ssh-keygen -t rsa -b 4096 -C "$ADMIN_EMAIL" -f "$SSH_KEY_PATH" -N ""
        print_success "SSH key generated"
    else
        print_success "SSH key already exists"
    fi
    
    # Display public key for DigitalOcean
    print_info "Your SSH public key (copy this to DigitalOcean):"
    echo -e "${CYAN}$(cat ${SSH_KEY_PATH}.pub)${NC}"
    echo ""
}

################################################################################
# PHASE 2: SERVER INITIAL SETUP (Run on server)
################################################################################

generate_server_setup_script() {
    print_header "GENERATING SERVER SETUP SCRIPT"
    
    cat > server-setup.sh << 'SETUP_SCRIPT'
#!/bin/bash

set -e

echo "=== SERVER SETUP SCRIPT ==="
echo "This script will install all required software"
echo ""

# Update system
echo "Updating system packages..."
apt update
apt upgrade -y

# Install Nginx
echo "Installing Nginx..."
apt install nginx -y
systemctl start nginx
systemctl enable nginx

# Add PHP repository
echo "Adding PHP repository..."
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.2 and extensions
echo "Installing PHP 8.2 and extensions..."
apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
php8.2-redis php8.2-intl php8.2-soap -y

systemctl start php8.2-fpm
systemctl enable php8.2-fpm

# Install MySQL
echo "Installing MySQL..."
apt install mysql-server -y

# Install Redis
echo "Installing Redis..."
apt install redis-server -y
systemctl start redis
systemctl enable redis

# Install Composer
echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js 18.x
echo "Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install nodejs -y

# Install Supervisor
echo "Installing Supervisor..."
apt install supervisor -y
systemctl start supervisor
systemctl enable supervisor

# Install Certbot for SSL
echo "Installing Certbot..."
apt install certbot python3-certbot-nginx -y

# Configure firewall
echo "Configuring firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# Create deployer user
echo "Creating deployer user..."
if ! id "deployer" &>/dev/null; then
    adduser --disabled-password --gecos "" deployer
    usermod -aG sudo deployer
    usermod -aG www-data deployer
    
    # Set up sudo without password for deployer
    echo "deployer ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/deployer
fi

echo ""
echo "=== SERVER SETUP COMPLETE ==="
echo "Next: Run the deployment script"
SETUP_SCRIPT

    chmod +x server-setup.sh
    print_success "Server setup script created: server-setup.sh"
}

################################################################################
# PHASE 3: DEPLOY APPLICATION
################################################################################

deploy_application() {
    print_header "PHASE 3: DEPLOYING APPLICATION TO SERVER"
    
    # Transfer deployment package
    print_info "Transferring application to server..."
    scp -i "$SSH_KEY_PATH" laravel-crm-deploy.tar.gz ${SERVER_USER}@${SERVER_IP}:/home/${SERVER_USER}/
    print_success "Application transferred"
    
    # Transfer and run deployment script on server
    print_info "Running deployment on server..."
    
    ssh -i "$SSH_KEY_PATH" ${SERVER_USER}@${SERVER_IP} << DEPLOY_COMMANDS
set -e

# Create application directory
sudo mkdir -p $REMOTE_APP_PATH
sudo chown ${SERVER_USER}:www-data $REMOTE_APP_PATH

# Extract application
cd /home/${SERVER_USER}
tar -xzf laravel-crm-deploy.tar.gz
sudo mv laravel-crm-2.1.5/* $REMOTE_APP_PATH/
rm -rf laravel-crm-2.1.5 laravel-crm-deploy.tar.gz

# Set up environment
cd $REMOTE_APP_PATH
cp .env.example .env

# Update .env file
sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN_NAME}|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env
sed -i "s|CACHE_DRIVER=.*|CACHE_DRIVER=redis|" .env
sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=redis|" .env
sed -i "s|QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" .env

# Add production settings
echo "" >> .env
echo "SESSION_SECURE_COOKIE=true" >> .env
echo "SESSION_SAME_SITE=strict" >> .env

# Install dependencies
composer install --optimize-autoloader --no-dev --no-interaction

# Generate application key
php artisan key:generate --force

# Install Node dependencies and build assets
npm install --production
npm run build

# Build admin assets
cd packages/Ispecia/Admin
npm install
npm run build
cd ../../..

# Set up database
sudo mysql << MYSQL_COMMANDS
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_COMMANDS

# Run migrations
php artisan migrate --force

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
composer dump-autoload --optimize --classmap-authoritative

# Set permissions
sudo chown -R ${SERVER_USER}:www-data .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache

echo "Application deployed successfully!"
DEPLOY_COMMANDS

    print_success "Application deployed to server"
}

################################################################################
# PHASE 4: CONFIGURE WEB SERVER
################################################################################

configure_webserver() {
    print_header "PHASE 4: CONFIGURING WEB SERVER"
    
    print_info "Configuring Nginx..."
    
    ssh -i "$SSH_KEY_PATH" ${SERVER_USER}@${SERVER_IP} << 'NGINX_CONFIG'
# Create Nginx configuration
sudo tee /etc/nginx/sites-available/laravel-crm > /dev/null << 'EOF'
server {
    listen 80;
    server_name DOMAIN_PLACEHOLDER WWW_DOMAIN_PLACEHOLDER;
    root REMOTE_APP_PATH_PLACEHOLDER/public;

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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;
    fastcgi_read_timeout 300;
}
EOF

# Replace placeholders
sudo sed -i "s|DOMAIN_PLACEHOLDER|${DOMAIN_NAME}|g" /etc/nginx/sites-available/laravel-crm
sudo sed -i "s|WWW_DOMAIN_PLACEHOLDER|${WWW_DOMAIN}|g" /etc/nginx/sites-available/laravel-crm
sudo sed -i "s|REMOTE_APP_PATH_PLACEHOLDER|${REMOTE_APP_PATH}|g" /etc/nginx/sites-available/laravel-crm

# Enable site
sudo ln -sf /etc/nginx/sites-available/laravel-crm /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Configure PHP-FPM
sudo sed -i "s/user = www-data/user = ${SERVER_USER}/" /etc/php/8.2/fpm/pool.d/www.conf
sudo sed -i "s/group = www-data/group = www-data/" /etc/php/8.2/fpm/pool.d/www.conf

# Test and reload
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm

echo "Nginx configured successfully!"
NGINX_CONFIG

    print_success "Nginx configured"
    
    # Install SSL certificate
    print_info "Installing SSL certificate..."
    print_warning "Make sure your domain DNS is pointing to ${SERVER_IP}"
    read -p "Press Enter when DNS is ready, or Ctrl+C to skip SSL..."
    
    ssh -i "$SSH_KEY_PATH" ${SERVER_USER}@${SERVER_IP} << SSL_INSTALL
sudo certbot --nginx -d ${DOMAIN_NAME} -d ${WWW_DOMAIN} \
    --non-interactive --agree-tos --email ${ADMIN_EMAIL} --redirect
echo "SSL certificate installed!"
SSL_INSTALL

    print_success "SSL certificate installed"
}

################################################################################
# PHASE 5: CONFIGURE PRODUCTION SERVICES
################################################################################

configure_services() {
    print_header "PHASE 5: CONFIGURING PRODUCTION SERVICES"
    
    print_info "Setting up queue workers..."
    
    ssh -i "$SSH_KEY_PATH" ${SERVER_USER}@${SERVER_IP} << 'SERVICES_CONFIG'
# Create Supervisor configuration for queue workers
sudo tee /etc/supervisor/conf.d/laravel-crm-worker.conf > /dev/null << 'EOF'
[program:laravel-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php REMOTE_APP_PATH_PLACEHOLDER/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --timeout=60
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=SERVER_USER_PLACEHOLDER
numprocs=4
redirect_stderr=true
stdout_logfile=REMOTE_APP_PATH_PLACEHOLDER/storage/logs/worker.log
stopwaitsecs=3600
startsecs=0
EOF

# Replace placeholders
sudo sed -i "s|REMOTE_APP_PATH_PLACEHOLDER|${REMOTE_APP_PATH}|g" /etc/supervisor/conf.d/laravel-crm-worker.conf
sudo sed -i "s|SERVER_USER_PLACEHOLDER|${SERVER_USER}|g" /etc/supervisor/conf.d/laravel-crm-worker.conf

# Start workers
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-crm-worker:*

echo "Queue workers started!"

# Create backup script
mkdir -p /home/${SERVER_USER}/backups
tee /home/${SERVER_USER}/backup-crm.sh > /dev/null << 'BACKUP_EOF'
#!/bin/bash
BACKUP_DIR="/home/SERVER_USER_PLACEHOLDER/backups"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u DB_USER_PLACEHOLDER -pDB_PASSWORD_PLACEHOLDER DB_NAME_PLACEHOLDER | gzip > $BACKUP_DIR/crm_$DATE.sql.gz
find $BACKUP_DIR -name "crm_*.sql.gz" -mtime +30 -delete
echo "[$(date)] Backup completed: crm_$DATE.sql.gz"
BACKUP_EOF

# Replace placeholders in backup script
sed -i "s|SERVER_USER_PLACEHOLDER|${SERVER_USER}|g" /home/${SERVER_USER}/backup-crm.sh
sed -i "s|DB_USER_PLACEHOLDER|${DB_USER}|g" /home/${SERVER_USER}/backup-crm.sh
sed -i "s|DB_PASSWORD_PLACEHOLDER|${DB_PASSWORD}|g" /home/${SERVER_USER}/backup-crm.sh
sed -i "s|DB_NAME_PLACEHOLDER|${DB_NAME}|g" /home/${SERVER_USER}/backup-crm.sh

chmod +x /home/${SERVER_USER}/backup-crm.sh

# Schedule daily backups
(crontab -l 2>/dev/null; echo "0 2 * * * /home/${SERVER_USER}/backup-crm.sh >> /var/log/crm-backup.log 2>&1") | crontab -

echo "Backup script configured!"

# Create health check script
tee /home/${SERVER_USER}/health-check.sh > /dev/null << 'HEALTH_EOF'
#!/bin/bash
echo "=== Laravel CRM Health Check ==="
echo "Time: $(date)"
echo ""
echo "1. Nginx:" && sudo systemctl is-active nginx
echo "2. PHP-FPM:" && sudo systemctl is-active php8.2-fpm
echo "3. MySQL:" && sudo systemctl is-active mysql
echo "4. Redis:" && redis-cli ping
echo "5. Queue Workers:" && sudo supervisorctl status | grep laravel-crm-worker
echo "6. Disk Space:" && df -h / | tail -1
echo ""
echo "=== Health Check Complete ==="
HEALTH_EOF

chmod +x /home/${SERVER_USER}/health-check.sh

echo "Health check script created!"
SERVICES_CONFIG

    print_success "Production services configured"
}

################################################################################
# PHASE 6: FINAL TESTING
################################################################################

final_testing() {
    print_header "PHASE 6: FINAL TESTING"
    
    print_info "Running health check..."
    ssh -i "$SSH_KEY_PATH" ${SERVER_USER}@${SERVER_IP} "/home/${SERVER_USER}/health-check.sh"
    
    print_info "Testing application..."
    echo ""
    print_success "Application URL: https://${DOMAIN_NAME}/admin"
    print_info "Default Login:"
    print_info "  Email: admin@example.com"
    print_info "  Password: admin123"
    echo ""
    
    print_info "Testing SSL..."
    curl -I "https://${DOMAIN_NAME}" 2>&1 | grep -E "HTTP|SSL"
    
    print_success "Deployment complete!"
}

################################################################################
# MAIN DEPLOYMENT FLOW
################################################################################

main() {
    clear
    print_header "🚀 AUTOMATED LARAVEL CRM DEPLOYMENT"
    
    echo -e "${CYAN}This script will deploy your Laravel CRM to a production server${NC}"
    echo -e "${CYAN}Please ensure you have:${NC}"
    echo -e "  1. Created a DigitalOcean droplet (Ubuntu 22.04)"
    echo -e "  2. Updated the configuration variables at the top of this script"
    echo -e "  3. Your domain DNS pointing to the server IP"
    echo ""
    
    read -p "Press Enter to continue or Ctrl+C to cancel..."
    
    # Check if configuration is updated
    if [ "$SERVER_IP" = "YOUR_SERVER_IP" ]; then
        print_error "Please update the configuration variables at the top of this script!"
        exit 1
    fi
    
    # Run all phases
    phase1_prepare_local
    generate_server_setup_script
    
    print_header "NEXT STEPS"
    echo "1. Copy server-setup.sh to your server:"
    echo -e "   ${CYAN}scp -i $SSH_KEY_PATH server-setup.sh root@${SERVER_IP}:~/${NC}"
    echo ""
    echo "2. SSH to your server and run the setup script:"
    echo -e "   ${CYAN}ssh -i $SSH_KEY_PATH root@${SERVER_IP}${NC}"
    echo -e "   ${CYAN}chmod +x server-setup.sh${NC}"
    echo -e "   ${CYAN}sudo ./server-setup.sh${NC}"
    echo ""
    echo "3. After server setup completes, press Enter to continue deployment..."
    read -p ""
    
    deploy_application
    configure_webserver
    configure_services
    final_testing
    
    print_header "🎉 DEPLOYMENT SUCCESSFUL!"
    echo -e "${GREEN}Your Laravel CRM is now live at: https://${DOMAIN_NAME}/admin${NC}"
    echo ""
    echo "Important commands:"
    echo "  - Health check: ssh ${SERVER_USER}@${SERVER_IP} './health-check.sh'"
    echo "  - View logs: ssh ${SERVER_USER}@${SERVER_IP} 'tail -f ${REMOTE_APP_PATH}/storage/logs/laravel.log'"
    echo "  - Restart workers: ssh ${SERVER_USER}@${SERVER_IP} 'sudo supervisorctl restart laravel-crm-worker:*'"
    echo ""
}

# Run main function
main
