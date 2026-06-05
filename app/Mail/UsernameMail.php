<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsernameMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $userEmail;

    public function __construct(string $userName, string $userEmail)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Username Anda - BUMDes',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.username',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
