# Laravel CRM Deployment Guide

This guide outlines the steps to deploy the Laravel CRM application to a production environment.

## 1. Server Requirements

Ensure your server meets the following requirements:

- **OS**: Linux (Ubuntu 20.04/22.04 recommended)
- **Web Server**: Nginx or Apache
- **PHP**: 8.1 or higher
  - Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Composer**: Latest version
- **Node.js**: LTS version (for building assets)
- **Supervisor**: For managing queue workers

## 2. Installation Steps

1.  **Install Dependencies**:

    ```bash
    composer install --optimize-autoloader --no-dev
    npm install && npm run build
    ```

2.  **Environment Configuration**:
    Copy the example env file:

    ```bash
    cp .env.example .env
    ```

    > [!IMPORTANT] > **APP_URL Configuration**
    > You MUST set `APP_URL` to your exact public domain (e.g., `https://crm.yourcompany.com`).
    > This is critical for **Email Tracking** and **VoIP Webhooks**. If this is incorrect, tracking pixels will fail and call events won't reach your server.

    Update `.env` with your production settings:

    ```ini
    APP_NAME="Your CRM"
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://crm.yourcompany.com

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_DATABASE=laravel_crm
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password
    ```

4.  **Generate Key & Migrate**:
    ```bash
    php artisan key:generate
    php artisan migrate --force
    php artisan db:seed --force
    php artisan storage:link
    php artisan optimize:clear
    ```

## 3. Email Configuration

Configure your SMTP provider in `.env`. This is required for sending emails and for the tracking pixel to work.

```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@yourcompany.com
MAIL_FROM_NAME="${APP_NAME}"
```

> [!NOTE] > **Email Tracking**: The system automatically injects a 1x1 tracking pixel into sent emails. The URL for this pixel is generated based on `APP_URL`. Ensure your domain supports HTTPS to avoid mixed content warnings in email clients.

## 4. VoIP Configuration

To enable calling features:

1.  **Provider Setup**: Configure your VoIP provider (e.g., Twilio) in the Admin panel under **Settings > VoIP**.
2.  **Webhook Configuration**:
    - In your provider's dashboard (e.g., Twilio Console), set the **Voice Webhook URL** to:
      `https://crm.yourcompany.com/api/voip/webhook`
    - Ensure the HTTP method is set to `POST`.

> [!WARNING] > **Webhook Accessibility**: Your server must be publicly accessible for webhooks to work. If you are behind a firewall, whitelist your provider's IP addresses.

## 5. Background Processes (Critical)

This application relies on background queues for sending emails and processing VoIP events.

### Supervisor Configuration

Install Supervisor:

```bash
sudo apt-get install supervisor
```

Create a configuration file `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

Start Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

> [!CAUTION] > **Queue Restart**: Whenever you change `.env` or deploy code changes, you **MUST** restart the queue worker for changes to take effect:
> `php artisan queue:restart`
> Failure to do this will result in emails sending with old configurations (e.g., wrong tracking links).

### Cron Schedule

Add the following to your server's crontab (`crontab -e`) to run scheduled tasks (like scheduled emails):

```bash
* * * * * cd /path/to/your/app && php artisan schedule:run >> /dev/null 2>&1
```

## 6. Security & Cautions

- **Debug Mode**: Always set `APP_DEBUG=false` in production to prevent leaking sensitive environment variables on error pages.
- **Permissions**: Ensure the `storage` and `bootstrap/cache` directories are writable by the web server user (usually `www-data`):
  ```bash
  chown -R www-data:www-data storage bootstrap/cache
  chmod -R 775 storage bootstrap/cache
  ```
- **HTTPS**: Always serve the application over HTTPS. Email tracking pixels and VoIP webhooks may be blocked or fail on insecure HTTP connections.
