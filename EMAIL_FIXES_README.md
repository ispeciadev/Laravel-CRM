# Email System Fixes - Summary

## Issues Fixed

### 1. Sent Emails Not Displaying ✅
**Problem**: 7 sent emails existed in database but were not showing in the Sent folder UI.

**Root Causes**:
- DataGrid query had SQL GROUP BY error (grouping by emails.id but selecting ungrouped columns)
- Tags column was inefficiently querying database for each row instead of using aggregated data

**Solutions Applied**:
- Fixed `EmailDataGrid.php` query to include all selected columns in GROUP BY clause
- Changed tags column to use `GROUP_CONCAT` aggregated data instead of per-row queries
- Cleared view and cache

**Files Modified**:
- `/packages/Ispecia/Admin/src/DataGrids/Mail/EmailDataGrid.php`

---

### 2. Schedule Email UI Not Visible ✅
**Problem**: Schedule Email datetime field existed in code but wasn't visible to users.

**Root Cause**: Cached views preventing UI updates from showing.

**Solution Applied**:
- Cleared compiled views: `php artisan view:clear`
- Cleared application cache: `php artisan cache:clear`
- Cleared config cache: `php artisan config:clear`

**Verification**: The schedule field is properly coded in `mail/index.blade.php` lines 515-527 with:
- Label: "Schedule Email (optional)"
- Input type: datetime-local
- Vue binding: v-model="draft.scheduled_at"

---

### 3. Scheduled Email Sending ✅
**Problem**: No functionality to actually send scheduled emails.

**Root Cause**: EmailController immediately sent all non-draft emails without checking scheduled_at field.

**Solutions Applied**:

1. **Created SendScheduledEmail Job** (`/packages/Ispecia/Email/src/Jobs/SendScheduledEmail.php`):
   - Queued job that sends email at scheduled time
   - Updates email folders to ['sent'] after successful send
   - Logs errors if sending fails

2. **Updated EmailController** (`/packages/Ispecia/Admin/src/Http/Controllers/Mail/EmailController.php`):
   - Added validation for scheduled_at field
   - Check if email has future scheduled_at
   - If scheduled: dispatch SendScheduledEmail job with delay, mark as 'outbox'
   - If not scheduled: send immediately, mark as 'sent'

3. **Started Queue Worker**:
   - Background process running to process scheduled email jobs
   - Command: `php artisan queue:work --daemon`
   - Created helper script: `/queue-worker.sh`

---

## How to Use Scheduled Emails

1. **Compose an Email**: Click "Compose" button
2. **Fill Email Details**: To, Subject, Body, etc.
3. **Set Schedule Time**: Use the "Schedule Email (optional)" datetime picker to select future date/time
4. **Send**: Click Send button
5. **Check Outbox**: Scheduled emails will appear in Outbox folder until sent
6. **Automatic Sending**: Queue worker will send the email at scheduled time and move it to Sent folder

---

## Queue Worker Management

### Start Queue Worker
```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
flatpak-spawn --host php artisan queue:work --daemon
```

### Check if Queue Worker is Running
```bash
ps aux | grep "queue:work"
```

### Stop Queue Worker
```bash
pkill -f "queue:work"
```

### Alternative: Use Background Script
```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
./queue-worker.sh &
```

---

## Testing

### Test Sent Emails Display
1. Go to Mail → Sent folder
2. You should now see all 7 previously sent emails
3. Pagination should show "1 - 7 of 7"
4. Table should display email details

### Test Schedule UI
1. Click Compose button
2. Scroll down in the compose modal
3. You should see "Schedule Email (optional)" field with datetime picker
4. Select a future date/time

### Test Scheduled Sending
1. Compose an email
2. Set scheduled_at to 2-3 minutes from now
3. Click Send
4. Email should appear in Outbox folder
5. Check jobs table: `DB::table('jobs')->count()` should show 1
6. Wait for scheduled time
7. Email should move to Sent folder automatically

---

## Database Verification

### Check Sent Emails
```php
php artisan tinker
\Ispecia\Email\Models\Email::where('folders', 'like', '%sent%')->count();
\Ispecia\Email\Models\Email::where('folders', 'like', '%sent%')->get(['id', 'subject', 'folders', 'created_at']);
```

### Check Scheduled Emails
```php
\Ispecia\Email\Models\Email::whereNotNull('scheduled_at')->get(['id', 'subject', 'scheduled_at', 'folders']);
```

### Check Queue Jobs
```php
DB::table('jobs')->get();
```

---

## Important Notes

1. **Queue Worker Must Run**: For scheduled emails to send, the queue worker MUST be running in the background.

2. **.env Configuration**: Ensure these settings are in `.env`:
   ```
   QUEUE_CONNECTION=database
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=abhishek143saini@gmail.com
   MAIL_PASSWORD=huvjnscdkbfpkgqz
   MAIL_ENCRYPTION=tls
   ```

3. **Folders Field Format**: The Email model casts 'folders' as array. In database it's stored as JSON string like `["sent"]`, but Laravel auto-converts to/from array.

4. **Time Zone**: Scheduled emails use Laravel's timezone setting from `config/app.php`. Ensure 'timezone' is set correctly.

---

## Files Modified Summary

1. `/packages/Ispecia/Admin/src/DataGrids/Mail/EmailDataGrid.php` - Fixed SQL query and tags column
2. `/packages/Ispecia/Admin/src/Http/Controllers/Mail/EmailController.php` - Added scheduling logic
3. `/packages/Ispecia/Email/src/Jobs/SendScheduledEmail.php` - NEW: Queue job for sending scheduled emails
4. `/queue-worker.sh` - NEW: Helper script to start queue worker

---

## Clear Caches (Run After Any Code Changes)

```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5
flatpak-spawn --host php artisan view:clear
flatpak-spawn --host php artisan cache:clear
flatpak-spawn --host php artisan config:clear
flatpak-spawn --host php artisan route:clear
```

---

## All Issues Resolved! ✅

- ✅ Sent emails now display properly in UI
- ✅ Schedule email field visible in compose modal
- ✅ Scheduled emails will send automatically via queue worker
- ✅ Queue worker running in background
- ✅ Deal module fully functional
- ✅ Lead status auto-conversion working
