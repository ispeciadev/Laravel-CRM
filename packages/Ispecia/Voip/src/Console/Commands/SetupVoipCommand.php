<?php

namespace Ispecia\Voip\Console\Commands;

use Illuminate\Console\Command;
use Ispecia\Voip\Models\VoipProvider;
use Ispecia\Voip\Services\VoipManager;

class SetupVoipCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'voip:setup
                            {--driver=twilio : Provider driver (twilio, telnyx, sip)}
                            {--interactive : Use interactive mode to enter credentials}';

    /**
     * The console command description.
     */
    protected $description = 'Quick setup wizard for VoIP provider configuration';

    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        parent::__construct();
        $this->voipManager = $voipManager;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔═══════════════════════════════════════╗');
        $this->info('║   VoIP System Setup Wizard            ║');
        $this->info('╚═══════════════════════════════════════╝');
        $this->newLine();

        // Check if provider already exists
        $existingProvider = VoipProvider::active()->first();
        if ($existingProvider) {
            $this->warn('⚠ Active provider already configured: ' . $existingProvider->name);
            if (!$this->confirm('Do you want to create a new provider?', false)) {
                $this->info('Setup cancelled.');
                return 0;
            }
        }

        $driver = $this->option('driver');
        $drivers = $this->voipManager->getAvailableDrivers();

        if (!isset($drivers[$driver])) {
            $driver = $this->choice(
                'Select VoIP provider',
                array_column($drivers, 'name'),
                0
            );
            $driver = array_search($driver, array_column($drivers, 'name'));
        }

        $driverInfo = $drivers[$driver];
        $this->info('Selected: ' . $driverInfo['name']);
        $this->info('Description: ' . $driverInfo['description']);
        $this->newLine();

        // Collect credentials
        $config = [];
        
        if ($this->option('interactive') || !config('voip.twilio.sid')) {
            $this->info('Enter provider credentials:');
            $this->newLine();

            foreach ($driverInfo['fields'] as $key => $field) {
                if ($field['required']) {
                    if ($field['type'] === 'password') {
                        $config[$key] = $this->secret($field['label'] . ' (required)');
                    } else {
                        $config[$key] = $this->ask($field['label'] . ' (required)');
                    }
                } else {
                    if ($field['type'] === 'password') {
                        $value = $this->secret($field['label'] . ' (optional, press Enter to skip)');
                    } else {
                        $value = $this->ask($field['label'] . ' (optional, press Enter to skip)');
                    }
                    if ($value) {
                        $config[$key] = $value;
                    }
                }
            }
        } else {
            // Try to use .env config
            $this->info('Using credentials from .env file...');
            
            if ($driver === 'twilio') {
                $config = [
                    'account_sid' => config('voip.twilio.sid'),
                    'auth_token' => config('voip.twilio.token'),
                    'api_key_sid' => config('voip.twilio.api_key'),
                    'api_key_secret' => config('voip.twilio.api_secret'),
                    'app_sid' => config('voip.twilio.app_sid'),
                    'from_number' => config('voip.twilio.number'),
                    'voice_region' => 'ashburn',
                ];
            }
        }

        $this->newLine();
        
        // Create provider
        try {
            $name = $this->ask('Provider name', $driverInfo['name'] . ' Provider');
            
            $provider = VoipProvider::create([
                'name' => $name,
                'driver' => $driver,
                'config' => $config,
                'is_active' => false,
                'priority' => 10,
            ]);

            $this->info('✓ Provider created successfully');
            $this->newLine();

            // Test connection
            if ($this->confirm('Test connection to provider?', true)) {
                $this->info('Testing connection...');
                
                try {
                    $providerInstance = $this->voipManager->getProviderById($provider->id);
                    $result = $providerInstance->testConnection();
                    
                    if ($result['success']) {
                        $this->info('✓ ' . $result['message']);
                        
                        if ($this->confirm('Activate this provider?', true)) {
                            $provider->activate();
                            $this->info('✓ Provider activated successfully');
                        }
                    } else {
                        $this->error('✗ ' . $result['message']);
                        $this->warn('Provider created but not activated due to connection failure.');
                        $this->info('You can edit credentials and test again from: Admin > VoIP > Providers');
                    }
                } catch (\Exception $e) {
                    $this->error('✗ Connection test failed: ' . $e->getMessage());
                }
            }

            $this->newLine();
            $this->info('═══════════════════════════════════════');
            $this->info('Setup Complete!');
            $this->info('═══════════════════════════════════════');
            $this->newLine();
            $this->info('Next steps:');
            $this->info('1. Visit: Admin > VoIP > Providers to manage providers');
            $this->info('2. Click the phone icon in the sidebar to open the softphone');
            $this->info('3. Make your first test call');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to create provider: ' . $e->getMessage());
            return 1;
        }
    }
}
