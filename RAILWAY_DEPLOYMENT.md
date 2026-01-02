# Deploy Laravel CRM to Railway.app (FREE)

## 📋 Prerequisites

- [Railway.app account](https://railway.app) (free sign-up)
- [GitHub account](https://github.com) (to host your code)
- Git installed locally
- Your Laravel CRM project ready

---

## ✅ Step 1: Prepare Your Project for Railway

### 1.1 Create a Procfile ✅ (Already Created)
File `Procfile` in project root:

```
web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force && php artisan storage:link
```

### 1.1b Create .user.ini (PHP Extensions) ✅ (Already Created)
File `.user.ini` in project root enables required PHP extensions:

```ini
extension=gd
extension=intl
extension=mbstring
extension=pdo_mysql
extension=openssl
extension=curl
extension=fileinfo
extension=tokenizer
extension=dom
extension=xml
extension=zip
```

This fixes the "GD extension missing" error from Railway build.

### 1.2 Update composer.json
Make sure these are in your `composer.json`:

```json
{
  "require": {
    "php": "^8.1"
  },
  "scripts": {
    "post-install-cmd": [
      "@php artisan vendor:publish --force",
      "@php artisan storage:link"
    ]
  }
}
```

### 1.3 Add .gitignore entries (already should have these)
```
.env
node_modules/
vendor/
storage/logs/*
```

### 1.4 Create .railwayignore (optional, speeds up deployment)
```
node_modules/
vendor/
storage/logs/*
.git/
tests/
phpunit.xml
```

---

## 🚀 Step 2: Push Code to GitHub

### 2.1 Create GitHub Repository
1. Go to [github.com/new](https://github.com/new)
2. Create a **public** repo named `laravel-crm`
3. Don't initialize with README (you have one)

### 2.2 Push Your Code
```bash
cd /home/Abhi/Downloads/laravel-crm-2.1.5

git remote add origin https://github.com/YOUR_USERNAME/laravel-crm.git
git branch -M main
git push -u origin main
```

**Replace `YOUR_USERNAME` with your GitHub username**

---

## 🚂 Step 3: Deploy to Railway

### 3.1 Connect Railway to GitHub
1. Go to [railway.app](https://railway.app)
2. Sign in with GitHub
3. Click **"New Project"**
4. Select **"Deploy from GitHub repo"**
5. Select your `laravel-crm` repository
6. Click **"Deploy"**

Railway will auto-detect it's a PHP/Laravel project.

### 3.2 Wait for Initial Build
- Railway will run `composer install`
- Build takes 3-5 minutes
- Check build logs in Railway dashboard

---

## 🔧 Step 4: Configure Environment Variables

### 4.1 Generate App Key Locally (if you haven't)
```bash
cd /home/Abhi/Downloads/laravel-crm-2.1.5
php artisan key:generate
cat .env | grep APP_KEY
```

Copy the `APP_KEY` value (looks like: `base64:xxx...`)

### 4.2 Add Variables in Railway Dashboard

In Railway, go to your project → **Variables** tab, add:

| Key | Value |
|-----|-------|
| `APP_KEY` | `base64:xxx...` (from above) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-app.railway.app` |
| `LOG_CHANNEL` | `stack` |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.mailtrap.io` (or SendGrid) |
| `MAIL_PORT` | `2525` |
| `MAIL_USERNAME` | `your_email` |
| `MAIL_PASSWORD` | `your_password` |
| `MAIL_FROM_ADDRESS` | `hello@ispecia.com` |

**Note:** Railway will auto-add `DATABASE_URL` from the MySQL plugin.

---

## 🗄️ Step 5: Add MySQL Database

### 5.1 Add Database Plugin
1. In Railway dashboard, click **"+ Add Service"**
2. Select **MySQL**
3. Railway creates a database automatically
4. Click on MySQL service → **Variables**
5. Copy the `DATABASE_URL`

### 5.2 Configure Database in Laravel
Railway automatically detects `DATABASE_URL`. Update your `config/database.php` to use it:

```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'railway'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

---

## 🏗️ Step 6: Build Admin Assets

### 6.1 Add Build Step
In your `Procfile`, update to:

```
web: vendor/bin/heroku-php-apache2 public/
release: composer install --no-dev && npm install && npm run build && php artisan migrate --force
```

Or better, in Railway dashboard:
1. Go to **Deployments** tab
2. Add **Build Commands**:
   ```
   npm install
   npm run build
   ```

3. Add **Start Command**:
   ```
   vendor/bin/heroku-php-apache2 public/
   ```

---

## ✨ Step 7: Post-Deployment

### 7.1 Run Migrations
Railway runs this automatically via `release` command, but you can manually run:

```bash
# Via Railway dashboard CLI or after first deployment
php artisan migrate --force
php artisan db:seed --force  # (optional, loads demo data)
```

### 7.2 Create Admin User
```bash
php artisan tinker
> User::factory()->admin()->create(['email' => 'admin@example.com', 'password' => bcrypt('password')])
> exit
```

Or use:
```bash
php artisan make:user  # If available in your commands
```

### 7.3 Set Up Cron Jobs (for scheduled tasks)
Railway doesn't support traditional cron. Instead, set up an external cron service:

**Option A: Use EasyCron (Free)**
1. Go to [easycron.com](https://www.easycron.com)
2. Create free account
3. Add cron job:
   - **URL**: `https://your-app.railway.app/api/schedule-runner`
   - **Schedule**: Every minute `* * * * *`

**Option B: Use SchedulerX (Free)**
- Go to [schedulerx.net](https://www.schedulerx.net)
- Similar setup to EasyCron

---

## 📧 Step 8: Configure Email (IMPORTANT)

### For SendGrid (Free tier: 100 emails/day)
1. Sign up at [sendgrid.com](https://sendgrid.com)
2. Create API key
3. In Railway Variables, set:
   ```
   MAIL_MAILER=sendgrid
   MAIL_HOST=smtp.sendgrid.net
   MAIL_PORT=587
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=your_sendgrid_api_key
   MAIL_FROM_ADDRESS=hello@ispecia.com
   ```

### For Mailtrap (Free tier: 500 emails/month)
1. Sign up at [mailtrap.io](https://mailtrap.io)
2. In Railway Variables, set:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   MAIL_FROM_ADDRESS=hello@ispecia.com
   ```

---

## 🎯 Step 9: Configure VoIP Webhook (if using Twilio)

1. Set up Twilio account
2. In Railway, go to **Settings** → **Domain**
3. Copy your Railway domain: `https://your-app.railway.app`
4. In Twilio Console, set webhook to:
   ```
   https://your-app.railway.app/api/voip/webhook
   ```

---

## ✅ Step 10: Deploy & Test

### 10.1 Trigger Deployment
Push changes to GitHub:
```bash
cd /home/Abhi/Downloads/laravel-crm-2.1.5
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

Railway automatically deploys!

### 10.2 Monitor Deployment
1. Go to Railway dashboard
2. Click **Deployments**
3. Watch build progress
4. Check logs if there are errors

### 10.3 Test Your App
```
https://your-app.railway.app
```

Login with admin credentials you created.

---

## 🐛 Troubleshooting

### Build Fails: "npm: command not found"
**Fix:** Railway may not have Node.js. Add to Railway buildpacks or use:
```
npm install --legacy-peer-deps
```

### Database Connection Error
**Fix:** Check `DATABASE_URL` in Variables. It should look like:
```
mysql://user:password@host:3306/database
```

### Assets Not Loading (CSS/JS broken)
**Fix:** Rebuild assets:
```bash
npm run build
git push origin main  # Triggers rebuild in Railway
```

### Emails Not Sending
**Fix:** 
1. Check MAIL_* variables are correct
2. Check spam folder
3. Verify sender domain in SendGrid/Mailtrap

### Storage Link Issues
**Fix:** Railway runs symlink command in release phase. If broken, run:
```bash
php artisan storage:link
```

---

## 📊 Pricing (Stay Free!)

**Your free monthly allocation:**
- $5 credit/month
- Database: ~$2/month
- Web dyno: ~$3/month
- **Total: You stay under $5!** ✅

---

## 🚀 Next Steps

1. **Follow Steps 1-10 above** ✅
2. **Test the app** at `https://your-app.railway.app`
3. **Set up custom domain** (optional):
   - Railway → Settings → Domains
   - Add your domain (e.g., `crm.yourcompany.com`)
   - Update DNS records provided by Railway

4. **Set up SSL** (automatic on Railway) ✅

---

**Need help?** Railway has excellent [documentation](https://docs.railway.app) and Discord community.

Good luck! 🎉
