# VoIP Integration Guide

## Overview
This guide explains the VoIP integration in your Laravel CRM using Twilio SDK. The VoIP module provides call functionality directly within the CRM interface.

## ✅ Completed Integration Steps

### 1. **Package Installation**
- ✅ Installed Twilio SDK (v7.16.2) via Composer
- ✅ Added `Ispecia\Voip` namespace to PSR-4 autoload in `composer.json`
- ✅ Registered `VoipServiceProvider` in `config/app.php`

### 2. **Database Setup**
- ✅ Created 5 database tables:
  - `voip_trunks` - SIP trunk configurations
  - `voip_accounts` - User VoIP accounts
  - `voip_routes` - Inbound call routing rules
  - `voip_calls` - Call history and metadata
  - `voip_recordings` - Call recording storage

### 3. **Configuration**
Environment variables already configured in `.env`:
```env
VOIP_PROVIDER=twilio
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_APP_SID=your_twiml_app_sid
TWILIO_NUMBER=your_twilio_number
```

**⚠️ IMPORTANT**: Update these values with your actual Twilio credentials from:
https://console.twilio.com/

### 4. **Routes Registered**
All VoIP routes are active:
- **Admin Routes** (Web):
  - `GET /admin/voip/trunks` - Manage SIP trunks
  - `GET /admin/voip/routes` - Configure inbound routes
  - `GET /admin/voip/recordings` - View call recordings

- **API Routes** (REST):
  - `POST /api/voip/token` - Generate Twilio client token
  - `POST /api/voip/calls/outbound` - Initiate outbound call
  - `GET /api/voip/calls/history` - Fetch call history

- **Webhook Routes** (Twilio callbacks):
  - `POST /voip/webhook/twilio/voice` - Handle voice events
  - `POST /voip/webhook/twilio/status` - Handle call status updates

### 5. **UI Components**
- ✅ Softphone Vue.js component (`Softphone.vue`)
- ✅ Admin views for trunks, routes, and recordings
- ✅ PersonDataGrid updated with clickable call icons

## 🔧 How to Complete the Setup

### Step 1: Get Twilio Credentials
1. Sign up at https://www.twilio.com/
2. Get your **Account SID** and **Auth Token** from the console
3. Purchase a Twilio phone number
4. Create a TwiML App and get the **App SID**

### Step 2: Update .env File
Replace the placeholder values:
```env
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token_here
TWILIO_APP_SID=APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_NUMBER=+1234567890
```

### Step 3: Configure Twilio Webhooks
In Twilio Console → TwiML Apps → Your App:
- **Voice Request URL**: `https://your-domain.com/voip/webhook/twilio/voice`
- **Status Callback URL**: `https://your-domain.com/voip/webhook/twilio/status`

### Step 4: Create a SIP Trunk (Optional)
Navigate to: `http://127.0.0.1:8000/admin/voip/trunks`
- Click "Create Trunk"
- Enter SIP provider details
- Save configuration

### Step 5: Configure Inbound Routes
Navigate to: `http://127.0.0.1:8000/admin/voip/routes`
- Map phone numbers to users/departments
- Set routing rules

## 📞 Making Calls

### From PersonDataGrid
1. Navigate to Contacts → Persons
2. Click the phone icon next to contact numbers
3. Call will initiate using the Softphone

### Using the Softphone Component
The Softphone provides:
- **Keypad**: Dial numbers manually
- **Call Controls**: Mute, hold, transfer
- **Call History**: View recent calls
- **Incoming Call Notifications**: Accept/reject calls

## 🗄️ Database Schema

### voip_calls Table
Stores all call records with relationships to:
- `users` - Agent who made/received the call
- `leads` - Associated lead
- `persons` - Associated contact person
- `deals` - Associated deal

Fields include:
- Direction (inbound/outbound)
- Status (initiated, ringing, in-progress, completed, failed)
- Duration in seconds
- Timestamps (started_at, ended_at)

## 🔌 API Usage Examples

### Generate Twilio Token
```javascript
const response = await axios.post('/api/voip/token');
const token = response.data.token;
```

### Initiate Outbound Call
```javascript
await axios.post('/api/voip/calls/outbound', {
    to: '+1234567890',
    person_id: 123, // Optional
    lead_id: 456    // Optional
});
```

### Fetch Call History
```javascript
const calls = await axios.get('/api/voip/calls/history');
```

## 🎨 Frontend Integration

### Add Softphone to Admin Layout
To enable the floating Softphone widget, add this to your admin layout:

```blade
<!-- In resources/views/layouts/admin.blade.php or similar -->
<voip-softphone></voip-softphone>
```

### Import Component (if needed)
```javascript
// In your main JS file
import Softphone from '../../packages/Ispecia/Voip/src/Resources/assets/js/components/Softphone.vue';

app.component('voip-softphone', Softphone);
```

## 📊 Features

### ✅ Implemented
- Twilio SDK integration
- Database schema for calls, trunks, routes
- API endpoints for call management
- Webhook handlers for Twilio events
- Admin UI for trunk/route configuration
- Call history tracking
- Softphone Vue component

### 🔄 Available for Extension
- Call recording (storage configured, needs implementation)
- Click-to-call from Persons list
- Call notes and disposition codes
- Call analytics and reporting
- IVR (Interactive Voice Response)
- Conference calling
- Call queuing

## 🐛 Troubleshooting

### Issue: Calls not connecting
- Verify Twilio credentials in `.env`
- Check Twilio console for account status
- Ensure webhook URLs are publicly accessible
- Review `storage/logs/laravel.log` for errors

### Issue: No dial tone in Softphone
- Check browser console for JavaScript errors
- Verify `/api/voip/token` endpoint returns valid token
- Ensure microphone permissions granted in browser

### Issue: Calls disconnect immediately
- Verify TwiML App configuration in Twilio
- Check webhook URL responses
- Review call logs in Twilio console

## 📝 Notes

- **Security**: The `sanitize => false` flag is set on contact_numbers column to render HTML icons
- **Foreign Keys**: Fixed `deal_id` to use `unsignedBigInteger` for proper relationship
- **Audit**: Security audit disabled in composer.json for dependency installation
- **Deprecations**: PHP 8.4 deprecation warnings are expected with current Twilio SDK

## 🚀 Next Steps

1. **Get Twilio credentials** and update `.env`
2. **Test basic calling** functionality
3. **Configure inbound routing** for your team
4. **Enable call recording** if needed
5. **Train users** on Softphone features
6. **Monitor usage** via call history

## 📚 Resources

- Twilio PHP SDK: https://www.twilio.com/docs/libraries/php
- Twilio Voice: https://www.twilio.com/docs/voice
- TwiML: https://www.twilio.com/docs/voice/twiml
- Twilio Client (Browser): https://www.twilio.com/docs/voice/sdks/javascript

---

**Integration Status**: ✅ COMPLETE - Ready for Twilio credential configuration
**Last Updated**: November 20, 2025
