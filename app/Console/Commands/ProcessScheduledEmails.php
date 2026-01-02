<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ispecia\Email\Jobs\SendScheduledEmail;
use Ispecia\Email\Models\Email;

class ProcessScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all past scheduled emails that are still in outbox';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for scheduled emails to send...');

        // Find all emails that are scheduled in the past and still in outbox
        $emails = Email::whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->where('folders', 'like', '%outbox%')
            ->get();

        if ($emails->isEmpty()) {
            $this->info('No scheduled emails to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$emails->count()} scheduled email(s) to send.");

        foreach ($emails as $email) {
            try {
                SendScheduledEmail::dispatch($email->id);
                $this->line("✓ Dispatched job for email ID {$email->id}: {$email->subject}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to dispatch email ID {$email->id}: " . $e->getMessage());
            }
        }

        $this->info('Done!');
        return Command::SUCCESS;
    }
}
