<?php

namespace Ispecia\Email\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ispecia\Email\Mails\Email as EmailMailable;
use Ispecia\Email\Repositories\EmailRepository;

class SendScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected EmailRepository $emailRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $emails = $this->emailRepository->getModel()
            ->where('scheduled_at', '<=', now())
            ->whereJsonContains('folders', 'outbox')
            ->get();

        foreach ($emails as $email) {
            try {
                Mail::send(new EmailMailable($email));

                $this->emailRepository->update([
                    'folders' => ['sent'],
                    'scheduled_at' => null, // Optional: clear scheduled_at to indicate it's done, or keep it for record.
                                            // If we keep it, we must ensure we don't send it again.
                                            // The 'folders' check handles that (outbox -> sent).
                ], $email->id);

                $this->info("Email sent: {$email->id}");
            } catch (\Exception $e) {
                Log::error('Scheduled email send failed', [
                    'email_id' => $email->id,
                    'message'  => $e->getMessage(),
                ]);
                
                $this->error("Failed to send email: {$email->id}");
            }
        }
    }
}
