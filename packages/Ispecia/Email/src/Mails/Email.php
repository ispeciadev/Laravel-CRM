<?php

namespace Ispecia\Email\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email as MimeEmail;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new email instance.
     *
     * @return void
     */
    public function __construct(public $email) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from($this->email->from)
            ->to($this->email->reply_to)
            ->replyTo($this->email->parent_id ? $this->email->parent->unique_id : $this->email->unique_id)
            ->cc($this->email->cc ?? [])
            ->bcc($this->email->bcc ?? [])
            ->subject($this->email->parent_id ? $this->email->parent->subject : $this->email->subject);

        // Inject tracking pixel if tracking_hash exists
        $htmlContent = $this->email->reply;
        
        if ($this->email->tracking_hash) {
            // Manually construct URL to ensure we use the configured APP_URL (ngrok) 
            // instead of the request context (localhost)
            $baseUrl = rtrim(config('app.url'), '/');
            $trackingUrl = $baseUrl . '/email/track/' . $this->email->tracking_hash;
            
            // Add timestamp to prevent caching by email clients
            $trackingUrl .= '?t=' . time();
            
            \Illuminate\Support\Facades\Log::info('Injecting tracking pixel', [
                'email_id' => $this->email->id,
                'hash' => $this->email->tracking_hash,
                'url' => $trackingUrl,
                'app_url' => config('app.url')
            ]);

            $trackingPixel = '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none;" alt="" />';
            
            // Try to inject before closing body tag, otherwise append
            if (stripos($htmlContent, '</body>') !== false) {
                $htmlContent = str_ireplace('</body>', $trackingPixel . '</body>', $htmlContent);
            } else {
                $htmlContent .= $trackingPixel;
            }
        }
        
        $this->html($htmlContent);

        $this->withSymfonyMessage(function (MimeEmail $message) {
            $message->getHeaders()->addIdHeader('Message-ID', $this->email->message_id);

            $message->getHeaders()->addTextHeader('References', $this->email->parent_id
                ? implode(' ', $this->email->parent->reference_ids)
                : implode(' ', $this->email->reference_ids)
            );
        });

        foreach ($this->email->attachments as $attachment) {
            $this->attachFromStorage($attachment->path);
        }

        return $this;
    }
}
