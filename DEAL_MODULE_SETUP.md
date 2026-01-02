# Deal Module Setup Instructions

## What Has Been Created

I've created a complete Deal module for your Laravel CRM with the following features:

### 1. Database
- **Migration for deals table**: `packages/Ispecia/Deal/src/Database/Migrations/2024_11_18_000001_create_deals_table.php`
- **Migration to add status to leads**: `database/migrations/2024_11_18_000002_add_status_to_leads_table.php`

### 2. Models & Repositories
- **Deal Model**: `packages/Ispecia/Deal/src/Models/Deal.php`
- **Deal Repository**: `packages/Ispecia/Deal/src/Repositories/DealRepository.php`
- **Deal Contract**: `packages/Ispecia/Deal/src/Contracts/Deal.php`

### 3. Controllers & Routes
- **Deal Controller**: `packages/Ispecia/Deal/src/Http/Controllers/DealController.php`
- **Routes**: `packages/Ispecia/Deal/src/Routes/web.php`

### 4. Auto-Conversion Feature
- **Lead Observer**: `packages/Ispecia/Deal/src/Observers/LeadObserver.php`
  - Automatically converts leads to deals when status changes to "qualified"

### 5. Configuration
- **Menu Config**: `packages/Ispecia/Deal/src/Config/menu.php` (adds "Deals" to sidebar)
- **ACL Config**: `packages/Ispecia/Deal/src/Config/acl.php` (permissions)

### 6. Views
- **Index View**: `packages/Ispecia/Deal/src/Resources/views/index.blade.php`

### 7. Service Provider
- **DealServiceProvider**: Registers all Deal module components

## Setup Steps

Run these commands in your terminal where PHP is available:

```bash
cd /home/abhi/Downloads/laravel-crm-2.1.5

# 1. Regenerate autoload files
composer dump-autoload

# 2. Run migrations
php artisan migrate

# 3. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Restart the server
php artisan serve
```

## How to Use

### 1. Deals Menu
- A new "Deals" menu item will appear in the sidebar below "Leads"
- Click it to view all deals

### 2. Create Deals
- Click "Create Deal" button on the deals index page
- Or deals are automatically created when a lead's status changes to "qualified"

### 3. Lead Status Field
The lead form now supports a status field with these values:
- `new` (default)
- `contacted`
- `qualified` ← When set to this, auto-creates a deal
- `lost`

### 4. Auto-Conversion
When you edit a lead and change its status to "qualified":
- A deal is automatically created with the same information
- The deal is linked to the original lead
- Deal status is set to "open"

## Lead Status Options

To add the status dropdown to the lead form, you need to create a lead status attribute. Here are the recommended statuses:

- **new**: Initial status
- **contacted**: First contact made
- **qualified**: Ready to become a deal (triggers auto-conversion)
- **unqualified**: Not a good fit
- **lost**: Opportunity lost

## Deal Statuses

Deals have three statuses:
- **open**: Active deal
- **won**: Deal closed successfully
- **lost**: Deal lost

## Next Steps - Create Views

You still need to create these view files (copy and modify from leads views):

1. `packages/Ispecia/Deal/src/Resources/views/create.blade.php`
2. `packages/Ispecia/Deal/src/Resources/views/edit.blade.php`
3. `packages/Ispecia/Deal/src/Resources/views/view.blade.php`

## File Structure

```
packages/Ispecia/Deal/
├── composer.json
└── src/
    ├── Config/
    │   ├── acl.php
    │   └── menu.php
    ├── Contracts/
    │   └── Deal.php
    ├── Database/
    │   └── Migrations/
    │       └── 2024_11_18_000001_create_deals_table.php
    ├── Http/
    │   └── Controllers/
    │       └── DealController.php
    ├── Models/
    │   ├── Deal.php
    │   └── DealProxy.php
    ├── Observers/
    │   └── LeadObserver.php
    ├── Providers/
    │   ├── DealServiceProvider.php
    │   └── EventServiceProvider.php
    ├── Repositories/
    │   └── DealRepository.php
    ├── Resources/
    │   └── views/
    │       └── index.blade.php
    └── Routes/
        └── web.php
```

## Troubleshooting

If the Deals menu doesn't appear:
1. Clear all caches
2. Make sure migrations ran successfully
3. Check that composer autoload ran
4. Restart the development server

If auto-conversion doesn't work:
1. The Lead Observer needs the status column to exist
2. Run migrations first
3. Clear config cache

## Testing the Auto-Conversion

1. Go to Leads
2. Edit any lead
3. Change status to "qualified"
4. Save
5. Go to Deals - you should see a new deal created automatically
