<?php

namespace Ispecia\Email\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Ispecia\Email\Mails\Email;
use Ispecia\Email\Repositories\EmailRepository;

class SendScheduledEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $emailId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(EmailRepository $emailRepository): void
    {
        $email = $emailRepository->find($this->emailId);

        if (! $email) {
            return;
        }

        try {
            Mail::send(new Email($email));

            $emailRepository->update([
                'folders' => ['sent'],
                'sent_at' => now(),
            ], $email->id);
        } catch (\Exception $e) {
            // Log error or handle failure
            \Log::error('Failed to send scheduled email: ' . $e->getMessage());
            throw $e;
        }
    }
}
