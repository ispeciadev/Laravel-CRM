# VoIP Deployment Checklist

## Pre-Deployment

### 1. Local Development Setup
- [ ] Run `npm install` to install Twilio SDK
- [ ] Run `npm run dev` to build frontend assets
- [ ] Verify `@twilio/voice-sdk` in `package.json` dependencies
- [ ] Test softphone appears in bottom-right corner
- [ ] Verify all three tabs render (Contacts, Recent Calls, Keypad)

### 2. Twilio Account Setup
- [ ] Create Twilio account at https://www.twilio.com
- [ ] Purchase phone number with voice capabilities
- [ ] Create TwiML Application
  - [ ] Set Voice URL: `https://your-domain.com/voip/webhook/twilio/voice`
  - [ ] Set Status URL: `https://your-domain.com/voip/webhook/twilio/status`
  - [ ] Copy Application SID
- [ ] Create Standard API Key
  - [ ] Copy API Key SID
  - [ ] Copy API Secret (shown only once!)

### 3. Environment Configuration
- [ ] Add Twilio credentials to `.env`:
  ```env
  TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  TWILIO_AUTH_TOKEN=your_auth_token_here
  TWILIO_PHONE_NUMBER=+15551234567
  TWILIO_API_KEY=SKxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  TWILIO_API_SECRET=your_api_secret_here
  TWILIO_TWIML_APP_SID=APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  ```
- [ ] Run `php artisan config:clear`
- [ ] Verify config loads: `php artisan tinker` → `config('services.twilio')`

### 4. Database Migration (if needed)
- [ ] Check if VoIP tables exist: `voip_calls`, `voip_trunks`, `voip_routes`, `voip_recordings`
- [ ] Run migrations if needed: `php artisan migrate`

### 5. Local Testing
- [ ] Clear all caches:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan view:clear
  php artisan route:clear
  ```
- [ ] Test token generation: Visit `/api/voip/token` (should return JWT)
- [ ] Test contacts endpoint: Visit `/api/voip/contacts` (should return JSON array)
- [ ] Open CRM in Chrome/Firefox
- [ ] Grant microphone permissions
- [ ] Click floating phone button
- [ ] Verify contacts load from CRM
- [ ] Make test outbound call
- [ ] Check call appears in Recent Calls tab

## Production Deployment

### 6. Server Requirements
- [ ] **HTTPS enabled** (required for WebRTC)
- [ ] SSL certificate valid
- [ ] Firewall allows outbound HTTPS (443)
- [ ] Firewall allows WebRTC ports (UDP 16384-32768 recommended)
- [ ] PHP 8.1+ installed
- [ ] Node.js installed on build server

### 7. Build for Production
- [ ] Run `npm run build` (creates minified assets)
- [ ] Commit built assets or ensure build runs on deploy
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`

### 8. Deploy to Server
- [ ] Upload code to production server
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `npm install` (if building on server)
- [ ] Run `npm run build` (if building on server)
- [ ] Set correct file permissions
- [ ] Configure web server (Apache/Nginx)

### 9. Production Configuration
- [ ] Update `.env` with production Twilio credentials
- [ ] Set production webhook URLs in Twilio Console
  - [ ] Voice URL: `https://your-production-domain.com/voip/webhook/twilio/voice`
  - [ ] Status URL: `https://your-production-domain.com/voip/webhook/twilio/status`
- [ ] Configure Twilio phone number to use TwiML App
- [ ] Run production cache commands:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

### 10. Security Hardening
- [ ] Use strong `APP_KEY` in `.env`
- [ ] Consider webhook signature validation (optional)
- [ ] Set appropriate user permissions in CRM
- [ ] Review VoIP access permissions

### 11. Production Testing
- [ ] Open production CRM in browser
- [ ] Verify HTTPS padlock icon
- [ ] Grant microphone permissions
- [ ] Test softphone loads
- [ ] Make test outbound call
- [ ] Have someone call your Twilio number
- [ ] Verify incoming call rings in softphone
- [ ] Test call recording (if enabled)
- [ ] Check call appears in admin Recordings page
- [ ] Test click-to-call from Lead view
- [ ] Verify call logs save to database

### 12. Browser Compatibility
Test in supported browsers:
- [ ] Chrome 80+ (recommended)
- [ ] Firefox 75+
- [ ] Microsoft Edge 80+
- [ ] Safari 14+ (if needed)

### 13. Monitoring & Logging
- [ ] Set up Laravel error logging
- [ ] Monitor Twilio debugger for call issues
- [ ] Check `storage/logs/laravel.log` for errors
- [ ] Set up uptime monitoring for webhook endpoints
- [ ] Monitor Twilio usage and costs

### 14. User Training
- [ ] Share `VOIP_USER_GUIDE.md` with team
- [ ] Train users on softphone features
- [ ] Demonstrate click-to-call functionality
- [ ] Show admin how to manage trunks/routes/recordings
- [ ] Set expectations for browser requirements

### 15. Backup & Disaster Recovery
- [ ] Backup `.env` file securely
- [ ] Document Twilio TwiML App SID
- [ ] Document API Key SID (Secret cannot be retrieved)
- [ ] Test restore procedure
- [ ] Document rollback plan

## Post-Deployment

### 16. Performance Optimization
- [ ] Enable OPcache for PHP
- [ ] Configure Redis/Memcached for cache (optional)
- [ ] Optimize database indexes
- [ ] Set up CDN for static assets (optional)

### 17. Analytics & Metrics
- [ ] Track call volume
- [ ] Monitor call duration
- [ ] Track success/failure rates
- [ ] Monitor Twilio costs
- [ ] Review call recordings quality

### 18. Ongoing Maintenance
- [ ] Weekly check of Twilio debugger
- [ ] Monthly review of call recordings
- [ ] Update npm packages: `npm audit fix`
- [ ] Update composer packages: `composer update`
- [ ] Review and rotate API keys quarterly

## Troubleshooting Commands

```bash
# Check Twilio config
php artisan tinker
>>> config('services.twilio')

# Test token generation
curl -X POST https://your-domain.com/api/voip/token -H "Cookie: laravel_session=YOUR_SESSION"

# Check routes
php artisan route:list | grep voip

# View logs
tail -f storage/logs/laravel.log

# Clear everything
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# Rebuild assets
npm run build

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

## Emergency Contacts

- **Twilio Support:** https://support.twilio.com
- **Twilio Console:** https://console.twilio.com
- **Debugger:** https://console.twilio.com/monitor/logs/debugger
- **System Admin:** [Your contact]
- **Development Team:** [Your contact]

## Sign-Off

- [ ] All tests passed
- [ ] Production deployment successful
- [ ] Users trained
- [ ] Documentation complete
- [ ] Monitoring active
- [ ] Backup verified

**Deployed by:** ________________  
**Date:** ________________  
**Version:** 1.0  
**Production URL:** ________________
