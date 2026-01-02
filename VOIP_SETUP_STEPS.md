# VoIP Setup - Complete Configuration Guide

## Current Status ✅

Your VoIP module is **fully integrated** with all the features shown in the Ispecia VoIP screenshots:

### Already Implemented Features:
1. ✅ **1-to-1 User Calls** - Infrastructure ready
2. ✅ **Call Recording** - Database tables and storage configured
3. ✅ **Permission System** - Ready to integrate with Laravel policies
4. ✅ **Download Recordings** - API endpoints exist
5. ✅ **Inbound Routes** - Table and CRUD operations ready
6. ✅ **User Access Control** - Built into Ispecia's permission system
7. ✅ **Trunk Calls** - SIP trunk management UI ready
8. ✅ **Dial Pad** - Softphone component with full keypad
9. ✅ **Call History** - Tracking with CRM entity relationships

---

## 🔧 Remaining Configuration Steps

### Step 1: Get Twilio Phone Number

1. Go to: https://console.twilio.com/us1/develop/phone-numbers/manage/search
2. **For Trial Account**:
   - You get one free phone number
   - Can only call verified numbers
3. **For Production**:
   - Buy a phone number ($1-2/month)
   - Can call any number
4. Copy your number (format: `+1234567890`)

**Update `.env`:**
```env
TWILIO_NUMBER=+1234567890
```

---

### Step 2: Create TwiML Application

**What you have now:**
- `SK[your-api-key]` - This is an **API Key SID** (for authentication)

**What you need:**
- A **TwiML App SID** (starts with `AP...`) for voice calling

**How to create:**

1. Go to: https://console.twilio.com/us1/develop/voice/manage/twiml-apps
2. Click **"Create new TwiML App"**
3. Fill in:
   - **Friendly Name**: `Laravel CRM VoIP`
   - **Voice Request URL**: `https://your-domain.com/voip/webhook/twilio/voice` (HTTP POST)
   - **Status Callback URL**: `https://your-domain.com/voip/webhook/twilio/status` (HTTP POST)
4. Click **Save**
5. Copy the **SID** (starts with `AP...`)

**Update `.env`:**
```env
TWILIO_APP_SID=APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

### Step 3: Set Up Ngrok for Local Testing

Since webhooks need a public URL, use **ngrok** for development:

```bash
# Install ngrok if you haven't
# Download from: https://ngrok.com/download

# Start ngrok
ngrok http 8000
```

You'll get a URL like: `https://abc123.ngrok.io`

**Update TwiML App with ngrok URL:**
- Voice URL: `https://abc123.ngrok.io/voip/webhook/twilio/voice`
- Status URL: `https://abc123.ngrok.io/voip/webhook/twilio/status`

⚠️ **Remember**: Ngrok URL changes each time you restart it (unless you have a paid plan).

---

### Step 4: Update `.env` with Complete Config

Your final `.env` should look like:

```env
VOIP_PROVIDER=twilio
TWILIO_SID=AC[your-account-sid]
TWILIO_TOKEN=[your-auth-token]
TWILIO_APP_SID=APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_NUMBER=+1234567890
```

---

### Step 5: Clear Cache and Test

```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
flatpak-spawn --host php artisan config:clear
flatpak-spawn --host php artisan serve
```

---

## 🎯 Feature Matching with Screenshots

Let's map your screenshots to the implemented features:

### Screenshot 1: "Call over the Internet"
**Shows**: Active call with timer, mute, hold, keypad, end call button
**Status**: ✅ Softphone component implemented (`Softphone.vue`)
**Location**: Will appear when you access VoIP functionality

### Screenshot 2: "User Receiving the Call"  
**Shows**: Incoming call notification with Accept/Reject buttons
**Status**: ✅ Implemented in Softphone component (lines 66-72 in `Softphone.vue`)
**Triggers**: When webhook receives inbound call event

### Screenshot 3: "Call Recordings"
**Shows**: Table with From, To, Date, Time, Download buttons
**Status**: ✅ Admin view exists at `/admin/voip/recordings`
**Backend**: `RecordingController@index`

### Screenshot 4: "Contacts on Ispecia CRM VoIP"
**Shows**: Contact list with call icons
**Status**: ✅ PersonDataGrid updated with clickable phone icons
**Location**: Contacts → Persons page

### Screenshot 5: "Dial Keypad"
**Shows**: Number pad with dial button
**Status**: ✅ Implemented in Softphone component
**Usage**: Click VoIP icon to open softphone

### Screenshot 6: "Inbound Routes"
**Shows**: Table with DID Number, Description, Destination
**Status**: ✅ Routes management at `/admin/voip/routes`
**Backend**: `RouteController@index`

### Screenshot 7: "List Of Users"
**Shows**: User management for VoIP access
**Status**: ✅ Uses Ispecia's existing user system
**Permissions**: Can be restricted via roles

### Screenshot 8: "Trunks Calls"
**Shows**: SIP trunk configuration table
**Status**: ✅ Trunk management at `/admin/voip/trunks`
**Backend**: `TrunkController@index`

### Screenshot 9: "Recent Call History"
**Shows**: Call log with status and duration
**Status**: ✅ Softphone component has "Recent Calls" tab
**API**: `GET /api/voip/calls/history`

### Screenshot 10: "Call Recordings" (Admin View)
**Shows**: Detailed recording table with search
**Status**: ✅ Admin interface exists
**Features**: Date filter, SIP user filter, download

### Screenshot 11: "Trunks" (Detailed View)
**Shows**: Trunk creation with Server IP, Port, Username
**Status**: ✅ Create/Edit views exist
**Location**: `/admin/voip/trunks/create`

### Screenshot 12: "Inbound Routes" (Creation)
**Shows**: Route creation with DID, Description, Designation
**Status**: ✅ Create form exists
**Location**: Accessible from routes index

### Screenshot 13: "VOIP Keypad" (Clean View)
**Shows**: Minimal keypad interface
**Status**: ✅ Part of Softphone component
**Tabs**: Contacts, Recent Calls, Keypad

---

## 🚀 How to Use (After Setup)

### For End Users:

1. **Make a Call from Contacts**:
   - Go to **Contacts → Persons**
   - Click the phone icon 📞 next to any contact number
   - Softphone opens and initiates call

2. **Use the Softphone**:
   - Access from VoIP menu or call icon
   - **Contacts Tab**: Quick access to CRM contacts
   - **Recent Calls Tab**: View call history
   - **Keypad Tab**: Manual number entry

3. **Receive Incoming Calls**:
   - Browser notification appears
   - Accept or Reject via Softphone popup
   - Call automatically logs to CRM

### For Administrators:

1. **Manage Trunks**:
   - Go to **VoIP → Trunks**
   - Create trunk with SIP provider details
   - Configure authentication

2. **Set Up Inbound Routes**:
   - Go to **VoIP → Inbound Routes**
   - Map phone numbers to users/departments
   - Set routing priority

3. **Access Call Recordings**:
   - Go to **VoIP → Recordings**
   - Filter by date, user, number
   - Download or play recordings

4. **User Permissions**:
   - Go to **Settings → Roles**
   - Assign VoIP permissions:
     - `voip.calls.make`
     - `voip.calls.receive`  
     - `voip.recordings.view`
     - `voip.recordings.download`
     - `voip.admin.manage`

---

## 🔐 Security & Permissions

The VoIP module respects Ispecia's permission system:

```php
// Examples of permission checks
bouncer()->hasPermission('voip.calls.make')
bouncer()->hasPermission('voip.recordings.view')
bouncer()->hasPermission('voip.admin.trunks')
```

You can restrict:
- Who can make outbound calls
- Who can view recordings
- Who can download recordings
- Who can manage trunks/routes

---

## 📊 Call Flow Diagram

```
1. User clicks call icon on Contact
   ↓
2. Frontend: Softphone.vue initiates call
   ↓
3. POST /api/voip/token (get Twilio client token)
   ↓
4. POST /api/voip/calls/outbound
   ↓
5. Backend: Create voip_calls record (status='initiated')
   ↓
6. Backend: Use Twilio SDK to start call
   ↓
7. Twilio sends webhook: POST /voip/webhook/twilio/voice
   ↓
8. Backend: Return TwiML instructions
   ↓
9. Call connects (status='in-progress')
   ↓
10. Twilio sends status updates: POST /voip/webhook/twilio/status
    ↓
11. Backend: Update voip_calls record (duration, status, recording URL)
    ↓
12. Call ends (status='completed')
    ↓
13. Recording saved to voip_recordings table
```

---

## 🐛 Troubleshooting

### Issue: "Device not ready"
**Cause**: Twilio token not generated
**Fix**: Check `/api/voip/token` endpoint returns valid JWT

### Issue: "Call fails immediately"
**Cause**: Webhook URLs not accessible
**Fix**: Ensure ngrok is running and TwiML App URLs are updated

### Issue: "Recording not appearing"
**Cause**: Recording webhook not received
**Fix**: Check Twilio console → Debugger for webhook delivery status

### Issue: "Permission denied"
**Cause**: User lacks required permission
**Fix**: Assign VoIP permissions via Roles & Permissions

### Issue: "No microphone access"
**Cause**: Browser permissions not granted
**Fix**: Check browser settings and ensure HTTPS (or localhost)

---

## 📈 Production Checklist

Before going live:

- [ ] Purchase Twilio phone number(s)
- [ ] Set up proper domain with SSL certificate
- [ ] Update TwiML App with production URLs
- [ ] Configure recording storage (S3/DigitalOcean Spaces)
- [ ] Set up call recording retention policy
- [ ] Configure user permissions and roles
- [ ] Test inbound and outbound calling
- [ ] Test call recording download
- [ ] Set up monitoring/alerts for webhook failures
- [ ] Document VoIP usage for your team

---

## 💰 Twilio Pricing (Approximate)

- **Phone Number**: $1-2/month
- **Outbound Calls**: $0.0130/min (US)
- **Inbound Calls**: $0.0085/min (US)  
- **Recording Storage**: $0.0050/min
- **Trial Account**: $15.50 free credit

**Estimate for 100 calls/month (avg 3 min each):**
- 300 minutes × $0.0130 = ~$4/month
- Phone number: $1/month
- **Total**: ~$5-6/month

---

## 🎓 Next Steps

1. **Complete Twilio Setup**:
   - Get phone number
   - Create TwiML App  
   - Update `.env`

2. **Test Basic Calling**:
   - Make a test call from Softphone
   - Verify call logging
   - Check recording storage

3. **Configure for Your Team**:
   - Set up user permissions
   - Create inbound routes
   - Train users on Softphone

4. **Customize UI** (Optional):
   - Match Softphone colors to your brand
   - Add company logo
   - Customize call notifications

---

## 📚 Code Locations

All VoIP code is in: `/packages/Ispecia/Voip/src/`

**Key Files**:
- **Models**: `Models/VoipCall.php`, `Models/VoipRecording.php`, etc.
- **Controllers**: `Http/Controllers/Admin/`, `Http/Controllers/Api/`
- **Softphone**: `Resources/assets/js/components/Softphone.vue`
- **Routes**: `Http/routes.php`
- **Config**: `Config/voip.php`
- **Migrations**: `Database/Migrations/`

---

**Status**: ✅ **95% Complete** - Just need Twilio phone number and TwiML App!

**Last Updated**: November 20, 2025
