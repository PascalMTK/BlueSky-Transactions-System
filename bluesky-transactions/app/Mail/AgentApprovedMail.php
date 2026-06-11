<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $agent) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'BLUESKY — Bienvenue ! Votre adhésion est confirmée',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agent-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
