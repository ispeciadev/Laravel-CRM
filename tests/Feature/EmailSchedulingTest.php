<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Ispecia\Email\Mails\Email as EmailMailable;
use Ispecia\Email\Models\Email;
use Ispecia\User\Models\Admin;

class EmailSchedulingTest extends TestCase
{
    public function test_scheduled_email_is_not_sent_immediately()
    {
        Mail::fake();

        // Create a user to authenticate (if needed, but repository might not need it if we use it directly)
        // However, controller uses bouncer, so we might need to simulate a logged in user if we hit the endpoint.
        // For now, let's test the command logic primarily, and maybe the repository/model logic.
        
        // Let's test the command logic.
        // Create a scheduled email directly in DB
        $email = Email::create([
            'subject' => 'Scheduled Email',
            'source' => 'web',
            'user_type' => 'admin',
            'folders' => ['outbox'],
            'from' => ['admin@example.com'],
            'reply_to' => ['admin@example.com'],
            'scheduled_at' => now()->subMinute(), // Scheduled in the past, so it should be sent
            'message_id' => 'test-message-id-' . time(),
        ]);

        // Run the command
        Artisan::call('email:send-scheduled');

        // Assert email was sent
        Mail::assertSent(EmailMailable::class, function ($mail) use ($email) {
            return $mail->email->id === $email->id;
        });

        // Assert email folder is updated to sent
        $this->assertContains('sent', $email->refresh()->folders);
    }

    public function test_future_scheduled_email_is_not_sent()
    {
        Mail::fake();

        $email = Email::create([
            'subject' => 'Future Email',
            'source' => 'web',
            'user_type' => 'admin',
            'folders' => ['outbox'],
            'from' => ['admin@example.com'],
            'reply_to' => ['admin@example.com'],
            'scheduled_at' => now()->addMinute(), // Scheduled in future
            'message_id' => 'test-message-id-future-' . time(),
        ]);

        Artisan::call('email:send-scheduled');

        Mail::assertNotSent(EmailMailable::class);
        
        $this->assertContains('outbox', $email->refresh()->folders);
        $this->assertNotContains('sent', $email->folders);
    }
}
