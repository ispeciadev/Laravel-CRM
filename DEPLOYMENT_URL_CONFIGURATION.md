# Deployment URL/Domain Configuration Guide

Based on your Laravel CRM project, here are **all the places** where you need to change URLs/domains when deploying to production:

---

## 1. **Environment File (.env)** ⭐ MOST IMPORTANT

**File**: `.env`

```bash
# Application URL - CHANGE THIS
APP_URL=https://dann-nonparasitic-leonora.ngrok-free.dev  # ← Change to production domain
# Example: APP_URL=https://crm.yourdomain.com

# Session Domain - CHANGE THIS
SESSION_DOMAIN=  # ← Set to your domain
# Example: SESSION_DOMAIN=.yourdomain.com

# CORS Allowed Origins (if using API externally)
# Add to .env if needed:
SANCTUM_STATEFUL_DOMAINS=crm.yourdomain.com

# Email Tracking Base URL (if different from APP_URL)
VOIP_WEBHOOK_URL=${APP_URL}  # Will use APP_URL by default
```

**Why?**
- `APP_URL`: Used for generating absolute URLs, asset URLs, email links, tracking pixels
- `SESSION_DOMAIN`: Controls cookie domain for sessions (use `.yourdomain.com` to share across subdomains)

---

## 2. **VoIP Webhook URLs** (if using VoIP feature)

**File**: `packages/Ispecia/Voip/src/Config/voip.php`

```php
return [
    // Webhook base URL for Twilio callbacks
    'webhook_base_url' => env('VOIP_WEBHOOK_URL', config('app.url')),
];
```

**Add to `.env`**:
```bash
VOIP_WEBHOOK_URL=https://crm.yourdomain.com
```

**Why?**
Twilio/Telnyx need public URLs to send webhook callbacks (call status, recordings).

---

## 3. **Twilio Configuration** (if using VoIP)

**File**: `.env`

```bash
# Twilio Webhooks - UPDATE THESE IN TWILIO CONSOLE
# Status Callback URL: https://crm.yourdomain.com/voip/webhook/twilio/status
# Voice URL: https://crm.yourdomain.com/voip/webhook/twilio/voice
```

**Action Required**:
1. Log in to [Twilio Console](https://console.twilio.com)
2. Go to Phone Numbers → Active Numbers
3. Update **Voice & Fax** section:
   - Voice URL: `https://crm.yourdomain.com/voip/webhook/twilio/voice`
   - Status Callback URL: `https://crm.yourdomain.com/voip/webhook/twilio/status`

---

## 4. **Email Tracking Pixel URL**

**File**: `packages/Ispecia/Email/src/Mails/Email.php` (line ~45-50)

The tracking pixel uses:
```php
<img src="{{ route('email.track', ['hash' => $tracking_hash]) }}" ... />
```

This automatically uses `APP_URL` from `.env`, so just update `.env`.

**Generated URL Example**:
```
https://crm.yourdomain.com/email/track/abc123def456...
```

---

## 5. **Asset URLs** (CSS, JS, Images)

**File**: `config/app.php`

```php
'asset_url' => env('ASSET_URL'),
```

**Add to `.env`** (optional - if assets served from CDN):
```bash
# If using CDN
ASSET_URL=https://cdn.yourdomain.com

# If not using CDN, leave empty (uses APP_URL)
ASSET_URL=
```

---

## 6. **CORS Configuration** (if frontend is separate domain)

**File**: `config/cors.php`

If your frontend is on a different domain (e.g., `app.yourdomain.com` accessing API on `api.yourdomain.com`):

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],

'allowed_origins' => [
    'https://app.yourdomain.com',  // Add your frontend domain
],

'allowed_origins_patterns' => [
    '/^https:\/\/.*\.yourdomain\.com$/',  // Allow all subdomains
],
```

**Or use `.env`**:
```bash
SANCTUM_STATEFUL_DOMAINS=app.yourdomain.com,crm.yourdomain.com
```

---

## 7. **OAuth/Social Login Callback URLs** (if implemented)

**Example**: Google OAuth, Facebook Login, etc.

**File**: `.env`

```bash
# Google OAuth (example)
GOOGLE_REDIRECT_URI=https://crm.yourdomain.com/auth/google/callback

# Facebook OAuth (example)
FACEBOOK_REDIRECT_URI=https://crm.yourdomain.com/auth/facebook/callback
```

**Action Required**:
Update callback URLs in:
- [Google Cloud Console](https://console.cloud.google.com)
- [Facebook Developers](https://developers.facebook.com)

---

## 8. **Payment Gateway Webhooks** (if using payments)

**Example**: Stripe, PayPal

**File**: `.env`

```bash
# Stripe Webhook
STRIPE_WEBHOOK_SECRET=whsec_...
# Webhook URL to configure in Stripe Dashboard:
# https://crm.yourdomain.com/webhooks/stripe
```

**Action Required**:
Update webhook URLs in payment gateway dashboards.

---

## 9. **Mail Configuration** (SMTP/Email)

**File**: `.env`

```bash
MAIL_FROM_ADDRESS=noreply@yourdomain.com  # ← Change sender email
MAIL_FROM_NAME="${APP_NAME}"

# SMTP Settings (if using custom SMTP)
MAIL_HOST=smtp.yourdomain.com
```

---

## 10. **Database Connection** (Production DB)

**File**: `.env`

```bash
DB_CONNECTION=mysql
DB_HOST=your-production-db-host.com  # ← Change to production DB
DB_PORT=3306
DB_DATABASE=laravel_crm_production   # ← Change DB name
DB_USERNAME=prod_user                # ← Change username
DB_PASSWORD=strong_password_here     # ← Change password
```

---

## 11. **Queue/Job Worker URLs** (if using queues)

**File**: `.env`

```bash
QUEUE_CONNECTION=redis  # or database, sqs, etc.

# If using Redis
REDIS_HOST=your-redis-host.com  # ← Change to production Redis
REDIS_PASSWORD=redis_password
REDIS_PORT=6379
```

---

## 12. **Cache Configuration**

**File**: `.env`

```bash
CACHE_DRIVER=redis  # or file, database
CACHE_PREFIX=crm_prod_  # ← Add prefix to avoid conflicts
```

---

## 13. **Session Configuration**

**File**: `.env`

```bash
SESSION_DRIVER=redis  # Recommended for production
SESSION_LIFETIME=120
SESSION_DOMAIN=.yourdomain.com  # ← Share sessions across subdomains
SESSION_SECURE_COOKIE=true      # ← Enable for HTTPS
```

---

## 14. **Trusted Proxies** (if behind load balancer/CDN)

**File**: `app/Http/Middleware/TrustProxies.php`

```php
protected $proxies = '*';  // Trust all proxies (Cloudflare, AWS ELB, etc.)

// Or specify IPs:
protected $proxies = [
    '192.168.1.1',
    '10.0.0.0/8',
];
```

---

## 15. **Ngrok URL Removal** (CRITICAL)

**Current `.env`**:
```bash
APP_URL=https://dann-nonparasitic-leonora.ngrok-free.dev  # ← REMOVE THIS
```

**Replace with**:
```bash
APP_URL=https://crm.yourdomain.com  # Your production domain
```

**Why?**
Ngrok URLs are temporary and will break after deployment.

---

## 16. **Admin Panel Path** (Security)

**File**: `config/app.php`

```php
'admin_path' => env('ADMIN_PATH', 'admin'),  // Change to custom path
```

**Add to `.env`**:
```bash
ADMIN_PATH=secure-admin-panel-2024  # Custom admin path (security through obscurity)
```

**New Admin URL**:
```
https://crm.yourdomain.com/secure-admin-panel-2024
```

---

## 17. **API Documentation URLs** (if using Swagger/API docs)

**File**: `config/l5-swagger.php` (if installed)

Update base URL in Swagger config to match production.

---

## 18. **File Storage URLs** (if using S3/CloudFront)

**File**: `.env`

```bash
FILESYSTEM_DISK=s3  # or public, local

# S3 Configuration
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket.s3.amazonaws.com  # ← CDN URL if using CloudFront
```

---

## 19. **WebSocket/Broadcasting URLs** (if using Laravel Echo/Pusher)

**File**: `.env`

```bash
BROADCAST_DRIVER=pusher  # or redis, ably

# Pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=us2
PUSHER_HOST=  # Leave empty to use Pusher cloud
PUSHER_PORT=443
PUSHER_SCHEME=https

# Frontend needs to know WebSocket endpoint
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

---

## 20. **Frontend Build Configuration** (Vite)

**File**: `vite.config.js` (if using Vite)

```js
export default defineConfig({
    server: {
        host: 'localhost',  // Development only
        port: 5173,
    },
    build: {
        manifest: true,
        outDir: 'public/build',  // Assets will be in public/build
    },
});
```

**No changes needed** - Vite builds use relative paths by default.

---

## Quick Deployment Checklist

```bash
# 1. Update .env file
cp .env.example .env
nano .env  # Edit all URLs/domains

# 2. Generate application key
php artisan key:generate

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Build frontend assets
npm ci --production
npm run build

# 6. Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Link storage (if using public disk)
php artisan storage:link

# 8. Optimize for production
php artisan optimize

# 9. Queue worker (supervisor/systemd)
php artisan queue:work --daemon

# 10. Schedule cron job
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Summary Table

| Location | Setting | Example Value |
|----------|---------|---------------|
| `.env` | `APP_URL` | `https://crm.yourdomain.com` |
| `.env` | `SESSION_DOMAIN` | `.yourdomain.com` |
| `.env` | `VOIP_WEBHOOK_URL` | `https://crm.yourdomain.com` |
| `.env` | `MAIL_FROM_ADDRESS` | `noreply@yourdomain.com` |
| `.env` | `DB_HOST` | `production-db.mysql.com` |
| `.env` | `ASSET_URL` | `https://cdn.yourdomain.com` (optional) |
| Twilio Console | Voice URL | `https://crm.yourdomain.com/voip/webhook/twilio/voice` |
| Twilio Console | Status Callback | `https://crm.yourdomain.com/voip/webhook/twilio/status` |
| `config/app.php` | `admin_path` | Custom admin path |

---

## Production .env Template

Here's a minimal `.env` template with all URL/domain settings you need to change:

```bash
# ==============================================
# PRODUCTION ENVIRONMENT CONFIGURATION
# ==============================================

APP_NAME="Your CRM Name"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://crm.yourdomain.com  # ← CHANGE THIS

# ==============================================
# SESSION & SECURITY
# ==============================================
SESSION_DOMAIN=.yourdomain.com      # ← CHANGE THIS
SESSION_SECURE_COOKIE=true
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# ==============================================
# DATABASE
# ==============================================
DB_CONNECTION=mysql
DB_HOST=your-production-db-host.com  # ← CHANGE THIS
DB_PORT=3306
DB_DATABASE=laravel_crm_production   # ← CHANGE THIS
DB_USERNAME=prod_user                # ← CHANGE THIS
DB_PASSWORD=strong_password_here     # ← CHANGE THIS

# ==============================================
# CACHE & REDIS
# ==============================================
CACHE_DRIVER=redis
CACHE_PREFIX=crm_prod_
REDIS_HOST=your-redis-host.com       # ← CHANGE THIS
REDIS_PASSWORD=redis_password        # ← CHANGE THIS
REDIS_PORT=6379

# ==============================================
# MAIL
# ==============================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com        # ← CHANGE THIS
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username     # ← CHANGE THIS
MAIL_PASSWORD=your_smtp_password     # ← CHANGE THIS
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com  # ← CHANGE THIS
MAIL_FROM_NAME="${APP_NAME}"

# ==============================================
# VOIP WEBHOOKS (if using)
# ==============================================
VOIP_WEBHOOK_URL=https://crm.yourdomain.com  # ← CHANGE THIS

# ==============================================
# QUEUE
# ==============================================
QUEUE_CONNECTION=redis

# ==============================================
# ASSETS (optional - CDN)
# ==============================================
ASSET_URL=  # Leave empty or set CDN URL

# ==============================================
# SANCTUM (API)
# ==============================================
SANCTUM_STATEFUL_DOMAINS=crm.yourdomain.com  # ← CHANGE THIS

# ==============================================
# ADMIN PATH (security)
# ==============================================
ADMIN_PATH=admin  # Change to custom path for security

# ==============================================
# FILE STORAGE (if using S3)
# ==============================================
FILESYSTEM_DISK=local  # or s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_URL=

# ==============================================
# BROADCASTING (if using)
# ==============================================
BROADCAST_DRIVER=log  # or pusher, redis
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=us2
```

---

## Post-Deployment Verification

After deploying, test these critical URLs:

1. **Main Application**
   ```
   https://crm.yourdomain.com
   ```

2. **Admin Panel**
   ```
   https://crm.yourdomain.com/admin
   ```

3. **Email Tracking Pixel** (check sent email HTML source)
   ```
   https://crm.yourdomain.com/email/track/{hash}?t={timestamp}
   ```

4. **VoIP Webhooks** (if using)
   ```
   https://crm.yourdomain.com/voip/webhook/twilio/voice
   https://crm.yourdomain.com/voip/webhook/twilio/status
   ```

5. **Assets Loading**
   ```
   https://crm.yourdomain.com/build/assets/app-{hash}.css
   https://crm.yourdomain.com/build/assets/app-{hash}.js
   ```

6. **API Endpoints** (if using)
   ```
   https://crm.yourdomain.com/api/user
   ```

---

## Common Issues & Solutions

### Issue 1: Assets not loading (404 errors)
**Cause**: `APP_URL` incorrect or assets not built  
**Solution**: 
```bash
# Check APP_URL in .env
php artisan config:clear
npm run build
php artisan storage:link
```

### Issue 2: Email tracking pixel not loading
**Cause**: Route not cached or URL incorrect  
**Solution**:
```bash
php artisan route:clear
php artisan config:clear
# Check APP_URL in .env
```

### Issue 3: VoIP webhooks failing
**Cause**: Webhook URL not updated in Twilio console  
**Solution**: Update URLs in Twilio console to match production domain

### Issue 4: Sessions not persisting
**Cause**: `SESSION_DOMAIN` not set or incorrect  
**Solution**: Set `SESSION_DOMAIN=.yourdomain.com` in `.env`

### Issue 5: CORS errors
**Cause**: Frontend domain not in allowed origins  
**Solution**: Add domain to `SANCTUM_STATEFUL_DOMAINS` in `.env`

---

## CRITICAL: After Deployment

✅ Test email tracking pixel loads (check email source)  
✅ Test VoIP webhooks work (make test call)  
✅ Verify asset URLs resolve correctly (check browser console)  
✅ Confirm sessions persist across requests  
✅ Verify HTTPS works (SSL certificate installed)  
✅ Test login/logout functionality  
✅ Check all API endpoints respond correctly  
✅ Verify file uploads work  
✅ Test queue workers are processing jobs  
✅ Confirm scheduled tasks are running (check cron)  

---

**Last Updated**: November 25, 2025  
**Version**: 1.0
