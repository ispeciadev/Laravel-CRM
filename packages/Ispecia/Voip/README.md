# VoIP System - Setup & Usage Guide

## Quick Start (3 Easy Steps)

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Setup VoIP Provider (Choose ONE option)

#### Option A: Interactive Setup (Recommended)
```bash
php artisan voip:setup --interactive
```
Follow the prompts to enter your credentials.

#### Option B: Auto-configure from .env
If you have Twilio credentials in `.env`:
```bash
php artisan voip:migrate-config
```

#### Option C: Manual Setup via Admin Panel
1. Login to admin panel
2. Navigate to: **Settings → VoIP → Providers → Create**
3. Select provider (Twilio/Telnyx/SIP)
4. Enter credentials
5. Click **Test** to verify connection
6. Click **Activate** to enable

### Step 3: Start Making Calls!
1. Look for the **phone icon** in the sidebar
2. Click the **floating phone button** (bottom-right corner)
3. Click **Keypad** tab
4. Enter a phone number
5. Click **Call**

---

## Provider Credentials

### For Twilio:
Get from: https://console.twilio.com

Required:
- **Account SID**: ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
- **Auth Token**: Your auth token
- **From Number**: +15551234567 (your Twilio number)

Optional (recommended for production):
- **API Key SID**: SKxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
- **API Key Secret**: Your API secret
- **TwiML App SID**: APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

### For Telnyx:
Get from: https://portal.telnyx.com

Required:
- **API Key**: Your Telnyx API key
- **Connection ID**: Your connection ID
- **From Number**: +15551234567

### For Generic SIP:
Your SIP provider details:
- **SIP Server**: sip.yourprovider.com
- **Username**: Your SIP username
- **Password**: Your SIP password
- **Transport**: UDP/TCP/TLS

---

## Features

### ✓ Browser-Based Calling
Make and receive calls directly in your browser using WebRTC technology.

### ✓ Click-to-Call
Click any contact to instantly dial their number.

### ✓ Call History
View all your calls with:
- Contact names
- Call duration
- Direction (inbound/outbound)
- Status
- Recordings (if enabled)

### ✓ CRM Integration
Calls automatically link to:
- Leads
- Contacts (Persons)
- Deals

### ✓ Call Recordings
Automatically record calls (configurable).

### ✓ Inbound Call Routing
Route incoming calls based on DID numbers to:
- Specific users
- Voicemail
- IVR menus

### ✓ Multi-Provider Support
Switch between providers without code changes:
- Twilio
- Telnyx
- Generic SIP (Asterisk, FreeSWITCH, etc.)

---

## Configuration

### Enable/Disable Recording
In `.env`:
```env
VOIP_RECORDING_ENABLED=true
VOIP_RECORDING_STORAGE=local  # or 's3'
```

### Webhook URL (for production)
If behind proxy or custom domain:
```env
VOIP_WEBHOOK_URL=https://yourdomain.com
```

### Token Expiry
Default: 1 hour (3600 seconds)
```env
VOIP_TOKEN_TTL=3600
```

---

## Troubleshooting

### "No active VoIP provider configured"
**Solution**: You need to create and activate a provider.
```bash
php artisan voip:setup --interactive
```

### Softphone doesn't appear
**Solution**: Rebuild frontend assets
```bash
npm run build
```

### Calls not connecting
**Checklist**:
1. ✓ Provider is activated (Admin > VoIP > Providers)
2. ✓ Test connection passed
3. ✓ From number is valid
4. ✓ Browser microphone permission granted

### Webhooks failing (production)
**Solution**: 
1. Ensure webhook URL is publicly accessible
2. For local testing, use ngrok:
```bash
ngrok http 8000
# Then update VOIP_WEBHOOK_URL in .env
```

### Call history empty
**Possible causes**:
- No permissions (need `voip.all_calls` to see others' calls)
- Webhooks not reaching server
- Provider not sending status callbacks

---

## Permissions

Grant users access via: **Settings → Roles & Permissions**

Available permissions:
- `voip` - Access VoIP module
- `voip.providers.create` - Create providers
- `voip.providers.edit` - Edit providers
- `voip.providers.delete` - Delete providers
- `voip.all_calls` - View all users' calls (admins only)
- `voip.recordings.download` - Download call recordings

---

## CLI Commands

### Setup wizard
```bash
php artisan voip:setup --interactive
```

### Migrate from .env config
```bash
php artisan voip:migrate-config
```

### Generate test token
```bash
php artisan voip:generate-token {user_id}
```

### Database seeder (auto-setup from .env)
```bash
php artisan db:seed --class=Ispecia\\Voip\\Database\\Seeders\\VoipProviderSeeder
```

---

## API Endpoints

### Get client token
```http
POST /api/voip/token
Authorization: Bearer {token}
```

### Initiate outbound call
```http
POST /api/voip/calls/outbound
Content-Type: application/json

{
  "to_number": "+15551234567",
  "entity_type": "lead",  // optional
  "entity_id": 123        // optional
}
```

### Get call history
```http
GET /api/voip/calls/history?direction=outbound&status=completed
```

### Get contacts
```http
GET /api/voip/contacts?search=john
```

---

## Security Notes

### Credential Encryption
All provider credentials are encrypted using Laravel's encryption (`APP_KEY`).

**Important**: If you rotate `APP_KEY`, you must:
1. Backup provider configs
2. Delete and recreate providers

### Webhook Signature Validation
All webhooks from Twilio are validated using HMAC-SHA1 signatures.

Unauthorized requests are rejected with `401 Unauthorized`.

### CSRF Protection
All admin forms include CSRF tokens.

API routes use `auth` middleware - requires valid session or token.

---

## Architecture Overview

```
┌─────────────────┐
│  Vue Softphone  │ (Browser UI)
└────────┬────────┘
         │ WebRTC Audio
         ↓
┌─────────────────┐
│  Twilio/Voice   │ (Provider Servers)
│     Servers     │
└────────┬────────┘
         │ Webhooks
         ↓
┌─────────────────┐
│  Laravel API    │
│  VoipManager    │ ← Database Providers
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│  MySQL Database │
│  - voip_calls   │
│  - voip_providers│
└─────────────────┘
```

**Call Flow**:
1. User clicks Call in Softphone
2. Frontend requests token from `/api/voip/token`
3. Backend generates provider-specific token (JWT)
4. Softphone initializes WebRTC with token
5. User dials → Softphone connects via WebRTC
6. Backend records call in database
7. Provider sends webhooks for status updates
8. Database updated with duration/recordings

---

## Support

### Check System Status
Admin dashboard widget shows:
- Active provider
- Total calls today
- Recording storage usage

### Logs
Check Laravel logs:
```bash
tail -f storage/logs/laravel.log | grep -i voip
```

### Debug Mode
Enable debug endpoint:
```bash
# Visit: /admin/voip/debug/trunks
# Shows trunk count and records (JSON)
```

---

## Migration from Old System

If you were using hardcoded Twilio config:

```bash
# 1. Migrate .env credentials to database
php artisan voip:migrate-config

# 2. Test the new configuration
# Visit: Admin > VoIP > Providers > Test

# 3. Once confirmed, remove from .env:
# - TWILIO_SID
# - TWILIO_TOKEN
# - TWILIO_API_KEY
# - TWILIO_API_SECRET
# - TWILIO_APP_SID
# - TWILIO_NUMBER
```

All credentials now managed via admin panel!

---

## Production Checklist

- [ ] Migrations run (`php artisan migrate`)
- [ ] Provider configured and tested
- [ ] Frontend assets built (`npm run build`)
- [ ] Webhook URL configured (if behind proxy)
- [ ] HTTPS enabled (required for WebRTC)
- [ ] Firewall allows outbound connections to provider
- [ ] Recording storage configured (local or S3)
- [ ] Permissions assigned to users
- [ ] Test call successful
- [ ] Webhooks receiving status updates
- [ ] Call recordings saving correctly

---

**System Version**: 2.0 (Multi-Provider Architecture)  
**Last Updated**: November 24, 2025  
**Minimum Requirements**: Laravel 10, PHP 8.1+, MySQL 5.7+
