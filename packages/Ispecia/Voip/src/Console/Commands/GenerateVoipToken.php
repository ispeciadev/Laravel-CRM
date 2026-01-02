<?php

namespace Ispecia\Voip\Console\Commands;

use Illuminate\Console\Command;
use Ispecia\Voip\Services\VoipManager;

class GenerateVoipToken extends Command
{
    protected $signature = 'voip:generate-token {identity=test_user}';

    protected $description = 'Generate a VoIP client token for testing connectivity';

    protected $voipManager;

    public function __construct(VoipManager $voipManager)
    {
        parent::__construct();

        $this->voipManager = $voipManager;
    }

    public function handle()
    {
        $identity = $this->argument('identity');

        try {
            $provider = $this->voipManager->getActiveProvider();
            $token = $provider->generateClientToken($identity);

            if (!$token) {
                $this->error('Could not generate token. Check VoIP provider configuration.');
                return 1;
            }

            $this->info('Token generated for identity: ' . $identity);
            $this->info('Provider: ' . $provider->getProviderModel()->getDriverDisplayName());
            $this->line($token);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
