<?php

namespace Ispecia\Voip\Database\Seeders;

use Illuminate\Database\Seeder;
use Ispecia\Voip\Models\VoipProvider;

class VoipProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if a provider already exists
        if (VoipProvider::count() > 0) {
            $this->command->info('VoIP providers already exist. Skipping seeder.');
            return;
        }

        // Try to create provider from .env config
        $twilioSid = config('voip.twilio.sid');
        $twilioToken = config('voip.twilio.token');
        $twilioNumber = config('voip.twilio.number');

        if ($twilioSid && $twilioToken && $twilioNumber) {
            $provider = VoipProvider::create([
                'name' => 'Twilio (Auto-configured)',
                'driver' => 'twilio',
                'config' => [
                    'account_sid' => $twilioSid,
                    'auth_token' => $twilioToken,
                    'api_key_sid' => config('voip.twilio.api_key'),
                    'api_key_secret' => config('voip.twilio.api_secret'),
                    'app_sid' => config('voip.twilio.app_sid'),
                    'from_number' => $twilioNumber,
                    'voice_region' => 'ashburn',
                ],
                'is_active' => true,
                'priority' => 10,
            ]);

            $this->command->info('✓ Created Twilio provider from .env configuration');
            $this->command->info('  Provider: ' . $provider->name);
            $this->command->info('  Status: Active');
        } else {
            $this->command->warn('⚠ No Twilio credentials found in .env file');
            $this->command->info('  Please configure a VoIP provider via: Admin > VoIP > Providers');
        }
    }
}
