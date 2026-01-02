# VoIP Quick Start

## 1. Install & Build
```bash
npm install
npm run dev
```

## 2. Configure .env
```env
TWILIO_ACCOUNT_SID=ACxxxx...
TWILIO_AUTH_TOKEN=your_token
TWILIO_PHONE_NUMBER=+15551234567
TWILIO_API_KEY=SKxxxx...
TWILIO_API_SECRET=your_secret
TWILIO_TWIML_APP_SID=APxxxx...
```

## 3. Create TwiML App (Twilio Console)
- Voice URL: `https://your-domain.com/voip/webhook/twilio/voice`
- Status URL: `https://your-domain.com/voip/webhook/twilio/status`

## 4. Create API Key (Twilio Console)
- Account → API keys → Create Standard key
- Copy SID and Secret to .env

## 5. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 6. Test
1. Open CRM in Chrome/Firefox
2. Grant microphone permission
3. Look for blue phone button (bottom-right)
4. Click to open softphone
5. Make a test call

## Quick Access

### Softphone Widget
- **Location:** Floating button (bottom-right corner)
- **Tabs:** Contacts | Recent Calls | Keypad
- **Click-to-Call:** Blue phone icons in Lead/Contact views

### Admin Pages
- **Trunks:** Settings → VoIP → Trunks
- **Inbound Routes:** Settings → VoIP → Inbound Routes  
- **Recordings:** Settings → VoIP → Recordings

### API Endpoints
- `POST /api/voip/token` - Get auth token
- `GET /api/voip/contacts` - List contacts
- `GET /api/voip/calls/history` - Call history

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Softphone not showing | `npm run dev` + hard refresh |
| Token error | Check API key in .env + `php artisan config:clear` |
| Call fails | Verify TwiML App SID + webhook URLs |
| No audio | Grant mic permissions + use HTTPS |

## Browser Support
✅ Chrome 80+  
✅ Firefox 75+  
✅ Edge 80+  
❌ Safari <14  
❌ IE (not supported)

## Documentation
- Full Guide: `VOIP_USER_GUIDE.md`
- Implementation: `VOIP_IMPLEMENTATION.md`
- Twilio Docs: https://www.twilio.com/docs/voice
