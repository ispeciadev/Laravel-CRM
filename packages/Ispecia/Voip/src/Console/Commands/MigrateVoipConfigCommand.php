<?php

namespace Ispecia\Voip\Console\Commands;

use Illuminate\Console\Command;
use Ispecia\Voip\Models\VoipProvider;
use Illuminate\Support\Facades\Config;

class MigrateVoipConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voip:migrate-config
                            {--force : Force migration even if provider already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate VoIP configuration from .env to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting VoIP configuration migration...');

        // Check if Twilio provider already exists
        $existingProvider = VoipProvider::where('driver', 'twilio')->first();

        if ($existingProvider && !$this->option('force')) {
            $this->warn('A Twilio provider already exists in the database.');
            if (!$this->confirm('Do you want to create another one?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        // Read Twilio config from .env
        $twilioConfig = [
            'account_sid' => config('voip.twilio.sid'),
            'auth_token' => config('voip.twilio.token'),
            'api_key_sid' => config('voip.twilio.api_key'),
            'api_key_secret' => config('voip.twilio.api_secret'),
            'app_sid' => config('voip.twilio.app_sid'),
            'from_number' => config('voip.twilio.number'),
            'voice_region' => 'ashburn', // Default
        ];

        // Validate required fields
        if (empty($twilioConfig['account_sid']) || empty($twilioConfig['auth_token'])) {
            $this->error('Missing required Twilio credentials in .env file.');
            $this->error('Please ensure TWILIO_SID and TWILIO_TOKEN are set.');
            return 1;
        }

        try {
            // Create provider
            $provider = VoipProvider::create([
                'name' => 'Twilio (Migrated from .env)',
                'driver' => 'twilio',
                'config' => $twilioConfig,
                'is_active' => true,
                'priority' => 10,
            ]);

            $this->info('✓ Successfully created Twilio provider in database');
            $this->info('  Provider ID: ' . $provider->id);
            $this->info('  Provider Name: ' . $provider->name);
            $this->info('  Status: ' . ($provider->is_active ? 'Active' : 'Inactive'));

            $this->newLine();
            $this->info('Migration completed successfully!');
            $this->newLine();

            $this->comment('Next steps:');
            $this->comment('1. Test the VoIP functionality to ensure it works with the new configuration');
            $this->comment('2. Once confirmed, you can remove the following from your .env file:');
            $this->comment('   - TWILIO_SID');
            $this->comment('   - TWILIO_TOKEN');
            $this->comment('   - TWILIO_API_KEY');
            $this->comment('   - TWILIO_API_SECRET');
            $this->comment('   - TWILIO_APP_SID');
            $this->comment('   - TWILIO_NUMBER');
            $this->newLine();
            $this->comment('3. You can now manage VoIP providers from the admin panel:');
            $this->comment('   Admin > VoIP > Providers');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to migrate configuration: ' . $e->getMessage());
            return 1;
        }
    }
}
