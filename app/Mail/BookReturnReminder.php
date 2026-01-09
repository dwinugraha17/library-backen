<?php

namespace App\Mail;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookReturnReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $borrowing;
    public $label;

    public function __construct(Borrowing $borrowing, $label)
    {
        $this->borrowing = $borrowing;
        $this->label = $label;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Pengembalian Buku (' . $this->label . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.return_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
