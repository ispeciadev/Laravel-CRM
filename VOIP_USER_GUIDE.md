# VoIP Feature - User Guide

## Overview

The ispecia CRM now includes a comprehensive VoIP (Voice over IP) integration powered by Twilio. This feature enables browser-based calling directly from your CRM interface.

## Features

### 1. **Floating Softphone Widget**
   - **Browser-based WebRTC calling** - No downloads required
   - **Three-tab interface**:
     - **Contacts**: Search and call CRM contacts
     - **Recent Calls**: View call history with status
     - **Keypad**: Dial numbers manually
   - **Floating toggle button** in bottom-right corner
   - **Call controls**: Mute, DTMF keypad, hangup
   - **Call timer** and status display

### 2. **Click-to-Call Integration**
   - Blue phone icon next to contact numbers in Lead/Contact views
   - One-click calling from anywhere in the CRM
   - Automatically opens softphone with number pre-filled

### 3. **VoIP Management**
   - **Trunks**: Manage SIP trunk connections
   - **Inbound Routes**: Configure call routing rules
   - **Call Recordings**: Browse, play, and download recordings

## Getting Started

### Prerequisites

1. **Twilio Account** - Sign up at [https://www.twilio.com](https://www.twilio.com)
2. **Twilio Phone Number** - Purchase a number with voice capabilities
3. **TwiML Application** - Create one in your Twilio Console
4. **API Credentials** - Get your Account SID and Auth Token

### Configuration

1. **Update `.env` file** with your Twilio credentials:

```env
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_API_KEY=SKxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_API_SECRET=your_api_secret_here
TWILIO_TWIML_APP_SID=APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

2. **Get API Key and Secret**:
   - Go to Twilio Console → Account → API keys & tokens
   - Create a new Standard API key
   - Save the SID and Secret

3. **Create TwiML Application**:
   - Go to Twilio Console → Voice → TwiML Apps
   - Create new TwiML App
   - Set Voice URL to: `https://your-domain.com/voip/webhook/twilio/voice`
   - Set Status Callback URL to: `https://your-domain.com/voip/webhook/twilio/status`
   - Copy the Application SID

4. **Clear caches and rebuild**:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
npm run dev  # or npm run build for production
```

## Using the Softphone

### Making Outbound Calls

**Method 1: From Contacts Tab**
1. Click the floating blue phone button (bottom-right)
2. Click "Contacts" tab
3. Search for a contact
4. Click the phone icon next to their name

**Method 2: From Keypad**
1. Open softphone
2. Click "Keypad" tab
3. Enter phone number
4. Click green call button

**Method 3: Click-to-Call**
1. Open a Lead or Contact record
2. Find phone number in contact information
3. Click blue phone icon next to number

### Receiving Incoming Calls

1. Browser will display notification (grant permission when prompted)
2. Softphone automatically opens showing caller info
3. Click green "Accept" button to answer
4. Click red "Reject" button to decline

### During a Call

- **Mute**: Toggle microphone on/off
- **Keypad**: Send DTMF tones (for IVR navigation)
- **Hangup**: End the call
- **Timer**: Shows call duration

### Recent Calls Tab

- View your call history
- See direction (inbound/outbound)
- Check call status (completed/missed/failed)
- View timestamps and duration
- Click any call to see details

## Admin Features

### VoIP Trunks

Navigate to: **Settings → VoIP → Trunks**

- Add multiple SIP trunks
- Configure provider settings (Twilio, SIP.us, etc.)
- Set trunk credentials
- Enable/disable trunks

### Inbound Routes

Navigate to: **Settings → VoIP → Inbound Routes**

- Create routing rules for incoming calls
- Pattern matching (DID numbers)
- Set destination (user, queue, voicemail)
- Priority ordering
- Active/inactive status

### Call Recordings

Navigate to: **Settings → VoIP → Recordings**

- View all recorded calls
- Play recordings in browser
- Download recordings as MP3
- See associated user and contact info
- Filter by date, direction, user

## Troubleshooting

### Softphone Not Appearing
1. Clear browser cache
2. Run `npm run dev` to rebuild assets
3. Check console for JavaScript errors
4. Verify Twilio credentials in `.env`

### "Token Generation Failed"
1. Verify `TWILIO_API_KEY` and `TWILIO_API_SECRET` are correct
2. Check API key is active in Twilio Console
3. Run `php artisan config:clear`

### "Call Failed" Error
1. Check `TWILIO_TWIML_APP_SID` is correct
2. Verify TwiML App Voice URL is set to your webhook
3. Check webhook URL is publicly accessible
4. Review Twilio debugger logs

### No Audio During Call
1. Grant microphone permissions in browser
2. Check microphone is not muted
3. Try different browser (Chrome/Firefox recommended)
4. Check firewall isn't blocking WebRTC

### Incoming Calls Not Ringing
1. Verify Twilio phone number is configured
2. Check incoming webhook is set correctly
3. Grant browser notification permissions
4. Check user has VoIP account configured

## Browser Compatibility

**Recommended:**
- Google Chrome 80+
- Mozilla Firefox 75+
- Microsoft Edge 80+

**Not Supported:**
- Internet Explorer
- Opera Mini
- Older Safari versions (<14)

## Security Notes

1. **HTTPS Required**: WebRTC only works over HTTPS in production
2. **API Keys**: Never commit `.env` file with real credentials
3. **Webhook Security**: Consider adding validation to webhook endpoints
4. **User Permissions**: Assign VoIP permissions appropriately

## API Endpoints

For developers integrating with the VoIP system:

- `POST /api/voip/token` - Generate authentication token
- `GET /api/voip/contacts` - Get callable contacts
- `GET /api/voip/calls/history` - Get call history
- `POST /api/voip/calls/outbound` - Initiate call (backend)

## Support

For issues or questions:
1. Check this guide first
2. Review Twilio debugger logs
3. Check browser console for errors
4. Contact your system administrator

## Advanced Configuration

### Custom Caller ID
Update in `TwilioVoipProvider.php`:
```php
'callerId' => config('services.twilio.caller_id', config('services.twilio.phone_number'))
```

### Call Recording
Enable automatic recording in TwiML App settings or modify webhook response.

### Call Queues
Extend `VoipRoute` model to support queue destinations.

### Multi-tenant Setup
Assign different Twilio numbers per organization/team.

---

**Version:** 1.0  
**Last Updated:** November 2025  
**Platform:** ispecia CRM
