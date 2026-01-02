# Custom Server Deployment Guide

This guide provides specific instructions for deploying Laravel CRM to various custom server environments.

## Table of Contents

1. [DigitalOcean Droplet](#digitalocean-droplet)
2. [AWS EC2](#aws-ec2)
3. [Linode](#linode)
4. [Vultr](#vultr)
5. [Generic VPS](#generic-vps)
6. [Shared Hosting](#shared-hosting)

---

## DigitalOcean Droplet

### 1. Create Droplet

-   **Recommended Size**: 2 GB RAM / 1 vCPU (minimum)
-   **OS**: Ubuntu 22.04 LTS
-   **Datacenter**: Choose closest to your users

### 2. Initial Server Setup

```bash
# SSH into your droplet
ssh root@your_droplet_ip

# Create non-root user
adduser deployer
usermod -aG sudo deployer

# Switch to new user
su - deployer
```

### 3. Follow Standard Deployment

Follow the main deployment workflow from step 1 (install software) onwards.

### 4. Configure Firewall

```bash
# DigitalOcean Cloud Firewall (recommended) or UFW
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 5. Point Domain to Droplet

-   In your domain registrar, create an A record pointing to your droplet's IP
-   Wait for DNS propagation (5-30 minutes)

---

## AWS EC2

### 1. Launch EC2 Instance

-   **AMI**: Ubuntu Server 22.04 LTS
-   **Instance Type**: t3.small or larger (2 GB RAM minimum)
-   **Security Group**: Allow ports 22, 80, 443

### 2. Connect to Instance

```bash
# Download your .pem key file
chmod 400 your-key.pem
ssh -i your-key.pem ubuntu@your-ec2-public-ip
```

### 3. Configure Security Group

In AWS Console:

-   **Inbound Rules**:
    -   SSH (22) - Your IP
    -   HTTP (80) - Anywhere
    -   HTTPS (443) - Anywhere

### 4. Elastic IP (Recommended)

-   Allocate an Elastic IP in AWS Console
-   Associate it with your EC2 instance
-   Use this IP for your domain's A record

### 5. Follow Standard Deployment

Continue with the standard deployment steps.

### 6. AWS-Specific Considerations

**RDS for Database (Optional):**

```bash
# Instead of local MySQL, use AWS RDS
# Update .env with RDS endpoint
DB_HOST=your-rds-endpoint.rds.amazonaws.com
DB_PORT=3306
```

**S3 for File Storage (Optional):**

```bash
# Install AWS SDK
composer require league/flysystem-aws-s3-v3

# Update .env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

---

## Linode

### 1. Create Linode

-   **Plan**: Linode 2GB or higher
-   **Image**: Ubuntu 22.04 LTS
-   **Region**: Choose closest to your users

### 2. Access via SSH

```bash
ssh root@your_linode_ip
```

### 3. Configure Firewall

```bash
# Use Linode Cloud Firewall or UFW
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 4. Follow Standard Deployment

Continue with standard deployment steps.

### 5. Linode-Specific Features

**Backups:**

-   Enable automatic backups in Linode dashboard
-   Or use the backup script from the main deployment guide

**NodeBalancer (for high traffic):**

-   Create NodeBalancer in Linode dashboard
-   Configure backend nodes
-   Update DNS to point to NodeBalancer IP

---

## Vultr

### 1. Deploy Server

-   **Server Type**: Cloud Compute
-   **Size**: 2 GB RAM minimum
-   **OS**: Ubuntu 22.04 x64

### 2. SSH Access

```bash
ssh root@your_vultr_ip
```

### 3. Firewall Configuration

```bash
# Configure firewall in Vultr dashboard or use UFW
sudo ufw allow 22,80,443/tcp
sudo ufw enable
```

### 4. Follow Standard Deployment

Proceed with standard deployment steps.

---

## Generic VPS

For any VPS provider (Hetzner, OVH, Contabo, etc.):

### Minimum Requirements

-   **RAM**: 2 GB (4 GB recommended)
-   **CPU**: 1 vCPU (2+ recommended)
-   **Storage**: 20 GB SSD
-   **OS**: Ubuntu 20.04/22.04 LTS

### Standard Setup Process

1. **Access Server:**

    ```bash
    ssh root@your_server_ip
    ```

2. **Update System:**

    ```bash
    apt update && apt upgrade -y
    ```

3. **Follow Main Deployment Guide:**
    - Install LEMP stack (Linux, Nginx, MySQL, PHP)
    - Deploy application
    - Configure SSL
    - Setup queue workers
    - Configure cron jobs

### Network Configuration

```bash
# Check if ports are open
sudo netstat -tulpn | grep LISTEN

# Test external access
curl -I http://your_server_ip
```

---

## Shared Hosting

**⚠️ WARNING**: Shared hosting has limitations. VoIP features may not work due to webhook restrictions.

### Requirements

-   **PHP**: 8.1+
-   **MySQL**: 5.7+
-   **SSH Access**: Required
-   **Composer**: Must be available
-   **Node.js**: For building assets (may need to build locally)

### Deployment Steps

1. **Build Assets Locally:**

    ```bash
    # On your local machine
    npm install
    npm run build
    ```

2. **Upload Files:**

    - Upload entire application via FTP/SFTP
    - Place in `public_html` or equivalent

3. **Configure Database:**

    - Create database via cPanel/Plesk
    - Import schema: `mysql -u user -p database < database.sql`

4. **Update .env:**

    ```bash
    # SSH into shared hosting
    cd public_html
    cp .env.example .env
    nano .env
    ```

5. **Run Artisan Commands:**

    ```bash
    php artisan key:generate
    php artisan migrate --force
    php artisan storage:link
    php artisan config:cache
    ```

6. **Setup Cron Job:**
    - In cPanel/Plesk, add cron job:
    ```
    * * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
    ```

### Shared Hosting Limitations

-   **No Supervisor**: Queue workers may not run continuously

    -   Alternative: Use cron to run `queue:work` every minute (not ideal)

    ```
    * * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty
    ```

-   **No VoIP**: Webhooks may be blocked by hosting provider

-   **Performance**: Limited resources may cause slowdowns

-   **SSL**: Use Let's Encrypt via cPanel/Plesk

---

## Docker Deployment (Alternative)

If you prefer containerized deployment:

### 1. Install Docker

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose
```

### 2. Create docker-compose.yml

```yaml
version: "3.8"

services:
    app:
        build:
            context: .
            dockerfile: Dockerfile
        ports:
            - "80:80"
        volumes:
            - ./storage:/var/www/html/storage
            - ./.env:/var/www/html/.env
        depends_on:
            - db
            - redis

    db:
        image: mysql:8.0
        environment:
            MYSQL_DATABASE: laravel_crm
            MYSQL_USER: crm_user
            MYSQL_PASSWORD: password
            MYSQL_ROOT_PASSWORD: rootpassword
        volumes:
            - db_data:/var/lib/mysql

    redis:
        image: redis:alpine

    queue:
        build:
            context: .
            dockerfile: Dockerfile
        command: php artisan queue:work
        volumes:
            - ./storage:/var/www/html/storage
            - ./.env:/var/www/html/.env
        depends_on:
            - db
            - redis

volumes:
    db_data:
```

### 3. Create Dockerfile

```dockerfile
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl zip unzip nginx supervisor \
    libpng-dev libonig-dev libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

### 4. Deploy

```bash
docker-compose up -d
```

---

## Post-Deployment Security Hardening

Regardless of hosting provider:

### 1. SSH Security

```bash
# Disable root login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no

# Use SSH keys instead of passwords
# Disable password authentication
# Set: PasswordAuthentication no

sudo systemctl restart sshd
```

### 2. Install Fail2Ban

```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Enable Automatic Security Updates

```bash
sudo apt install unattended-upgrades
sudo dpkg-reconfigure --priority=low unattended-upgrades
```

### 4. Configure Firewall

```bash
# Only allow necessary ports
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 5. Regular Updates

```bash
# Create update script
sudo nano /usr/local/bin/update-system.sh
```

```bash
#!/bin/bash
apt update
apt upgrade -y
apt autoremove -y
```

```bash
sudo chmod +x /usr/local/bin/update-system.sh

# Add to cron (weekly)
sudo crontab -e
# Add: 0 3 * * 0 /usr/local/bin/update-system.sh
```

---

## Performance Monitoring

### Install Monitoring Tools

```bash
# Install Netdata (real-time monitoring)
bash <(curl -Ss https://my-netdata.io/kickstart.sh)

# Access at: http://your_server_ip:19999
```

### Application Performance Monitoring

```bash
# Install Laravel Telescope (development only)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Log Monitoring

```bash
# Install logwatch
sudo apt install logwatch

# Configure daily email reports
sudo nano /etc/cron.daily/00logwatch
```

---

## Scaling Considerations

### Horizontal Scaling

1. **Load Balancer**: Use Nginx/HAProxy or cloud load balancer
2. **Separate Database Server**: Move MySQL to dedicated server
3. **Redis for Sessions**: Share sessions across multiple app servers
4. **Centralized File Storage**: Use S3/MinIO for uploads

### Vertical Scaling

-   Increase server resources (RAM, CPU)
-   Optimize PHP-FPM pool settings
-   Tune MySQL configuration
-   Enable OPcache and Redis

---

## Support Resources

-   **Main Deployment Guide**: See `DEPLOYMENT.md`
-   **Workflow**: See `.agent/workflows/deploy.md`
-   **VoIP Setup**: See `VOIP_QUICKSTART.md`
-   **Troubleshooting**: Check application logs in `storage/logs/`

For issues, check:

1. Application logs: `tail -f storage/logs/laravel.log`
2. Nginx logs: `tail -f /var/log/nginx/error.log`
3. PHP-FPM logs: `tail -f /var/log/php8.1-fpm.log`
4. Queue worker logs: `tail -f storage/logs/worker.log`
