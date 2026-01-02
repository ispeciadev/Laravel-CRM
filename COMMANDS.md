# Commands to Run - VoIP Setup

## Quick Setup (Automated)

```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
./setup-voip.sh
```

## Manual Setup (Step by Step)

### 1. Install npm Dependencies
```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
npm install
```

### 2. Build Frontend Assets

**For Development (with hot reload):**
```bash
npm run dev
```

**For Production (minified):**
```bash
npm run build
```

### 3. Clear Laravel Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 4. Configure Twilio Credentials

Edit `.env` file and add:

```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_PHONE_NUMBER=+15551234567
TWILIO_API_KEY=SKxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_API_SECRET=your_api_secret_here
TWILIO_TWIML_APP_SID=APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 5. Verify Configuration

```bash
php artisan tinker
```

Then in tinker:
```php
config('services.twilio')
exit
```

### 6. Test Token Generation

```bash
curl -X POST http://localhost/api/voip/token \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE"
```

### 7. Start Development Server (if needed)

```bash
php artisan serve
```

### 8. Open in Browser

Visit: http://localhost:8000/admin

- Look for blue phone button in bottom-right corner
- Grant microphone permissions
- Click to open softphone
- Start calling!

---

## Production Deployment Commands

### 1. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
```

### 2. Build Assets
```bash
npm run build
```

### 3. Set Production Environment
```bash
# In .env file
APP_ENV=production
APP_DEBUG=false
```

### 4. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Set File Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Configure Web Server

**For Nginx:**
```nginx
location /voip/webhook {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**For Apache:**
Already configured via `.htaccess`

---

## Troubleshooting Commands

### Check if Softphone is Loaded
```bash
# In browser console (F12)
document.querySelector('voip-softphone')
```

### View Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Check Routes
```bash
php artisan route:list | grep voip
```

### Rebuild Assets
```bash
rm -rf node_modules public/admin/build
npm install
npm run build
```

### Clear All Caches
```bash
php artisan optimize:clear
```

### Check Twilio Config
```bash
php artisan tinker --execute="dd(config('services.twilio'));"
```

### Test Contacts Endpoint
```bash
curl http://localhost/api/voip/contacts \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

### Test Call History Endpoint
```bash
curl http://localhost/api/voip/calls/history \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

---

## Database Commands (if needed)

### Check VoIP Tables
```bash
php artisan tinker
```

Then:
```php
DB::table('voip_calls')->count()
DB::table('voip_trunks')->count()
DB::table('voip_routes')->count()
DB::table('voip_recordings')->count()
exit
```

### Run Migrations (if tables don't exist)
```bash
php artisan migrate
```

---

## npm Scripts Available

```bash
# Development build with hot reload
npm run dev

# Production build (minified)
npm run build

# Check for security issues
npm audit

# Fix security issues
npm audit fix

# Update packages
npm update
```

---

## Quick Test Sequence

```bash
# 1. Clear everything
php artisan optimize:clear

# 2. Rebuild
npm run dev

# 3. Test token
curl -X POST http://localhost/api/voip/token

# 4. Open browser
# Visit http://localhost:8000/admin
# Look for blue phone button
```

---

## File Locations Reference

| File | Location |
|------|----------|
| Softphone Component | `packages/Ispecia/Voip/src/Resources/assets/js/components/Softphone.vue` |
| Contact API | `packages/Ispecia/Voip/src/Http/Controllers/Api/ContactController.php` |
| Call API | `packages/Ispecia/Voip/src/Http/Controllers/Api/CallController.php` |
| DataGrids | `packages/Ispecia/Voip/src/DataGrids/*.php` |
| Routes | `packages/Ispecia/Voip/src/Http/routes.php` |
| Click-to-Call | `packages/Ispecia/Admin/src/Resources/views/leads/view/person.blade.php` |
| Main Layout | `packages/Ispecia/Admin/src/Resources/views/components/layouts/index.blade.php` |

---

## Environment Variables Required

```env
# Required for VoIP
TWILIO_ACCOUNT_SID=        # From Twilio Console → Account Info
TWILIO_AUTH_TOKEN=         # From Twilio Console → Account Info
TWILIO_PHONE_NUMBER=       # Your Twilio phone number with +country code
TWILIO_API_KEY=            # Create in Console → Account → API Keys
TWILIO_API_SECRET=         # Shown only once when creating API key
TWILIO_TWIML_APP_SID=      # Create in Console → Voice → TwiML Apps
```

---

That's it! Follow these commands in order and you'll have VoIP working in your CRM. 🚀
