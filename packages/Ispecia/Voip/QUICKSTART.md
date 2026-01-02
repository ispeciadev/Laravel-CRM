# 🚀 QUICK START - Get VoIP Working in 2 Minutes

## For Users Who Want It to "Just Work"

### Method 1: I Have Twilio Credentials (Fastest - 1 Minute)

1. **Add credentials to `.env`**:
```env
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token_here
TWILIO_NUMBER=+15551234567
```

2. **Run setup**:
```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
php artisan voip:migrate-config
```

3. **Done!** Open your browser and click the phone icon 📞

---

### Method 2: Interactive Setup (Guided - 2 Minutes)

```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
php artisan voip:setup --interactive
```

Follow the prompts. System will:
- ✓ Ask for provider (Twilio/Telnyx/SIP)
- ✓ Collect credentials
- ✓ Test connection
- ✓ Activate provider automatically

**Done!** Start making calls.

---

### Method 3: One-Command Auto-Fix (If migrations not run)

```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
./packages/Ispecia/Voip/setup.sh
```

This script:
- ✓ Runs migrations
- ✓ Detects .env credentials
- ✓ Auto-creates provider
- ✓ Clears caches

---

## Don't Have Provider Credentials?

### Get Twilio Trial (Free - 5 Minutes)

1. **Sign up**: https://www.twilio.com/try-twilio
2. **Get free trial** ($15 credit)
3. **Find credentials** in console:
   - Account SID
   - Auth Token
4. **Get phone number** (free trial number)
5. **Copy to .env** and run setup

---

## Verify Everything Works

### Test Checklist:
```bash
# 1. Check migrations
php artisan migrate:status | grep voip

# 2. Check active provider
php artisan tinker
>>> \Ispecia\Voip\Models\VoipProvider::active()->first()->name

# 3. Build assets (if needed)
npm run build

# 4. Clear cache
php artisan config:clear
php artisan cache:clear
```

### Visual Test:
1. ✓ Phone icon appears in sidebar
2. ✓ Floating phone button (bottom-right)
3. ✓ Softphone opens when clicked
4. ✓ Can dial number
5. ✓ Microphone permission requested
6. ✓ Call connects

---

## Quick Troubleshooting

### "No active VoIP provider configured"
```bash
php artisan voip:setup --interactive
```

### "Class VoipProvider not found"
```bash
composer dump-autoload
php artisan migrate
```

### Phone icon not visible
```bash
php artisan config:clear
php artisan cache:clear
# Refresh browser (Ctrl+Shift+R)
```

### Softphone doesn't open
```bash
npm run build
# Clear browser cache
```

---

## Production Quick Setup

```bash
# 1. Migrations
php artisan migrate --force

# 2. Setup provider
php artisan voip:setup --interactive

# 3. Build assets
npm run build

# 4. Set webhook URL
# In .env:
VOIP_WEBHOOK_URL=https://yourdomain.com

# 5. Restart queue (if using)
php artisan queue:restart
```

---

## Support Commands

```bash
# Migrate from .env
php artisan voip:migrate-config

# Interactive setup
php artisan voip:setup --interactive

# Run seeder
php artisan db:seed --class=Ispecia\\Voip\\Database\\Seeders\\VoipProviderSeeder

# Generate token (test)
php artisan voip:generate-token 1
```

---

## Expected Result

After successful setup:

```
✓ Migrations: Complete
✓ Provider: Twilio (Active)
✓ Phone icon: Visible in sidebar
✓ Softphone: Opens on click
✓ Test call: Connects successfully
```

---

**Time to First Call**: ~2 minutes  
**Difficulty**: Easy  
**Requirements**: Twilio account (free trial works)

📝 **Full Documentation**: See `packages/Ispecia/Voip/README.md`
