<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Akatsuki Devs Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset',
            with: [
                'link' => $this->link,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
