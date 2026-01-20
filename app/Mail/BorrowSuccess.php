<?php

namespace App\Mail;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $borrowing;
    public $user;
    public $book;

    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
        $this->user = $borrowing->user;
        $this->book = $borrowing->book;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Berhasil Meminjam Buku: ' . $this->book->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.borrow_success',
        );
    }
}
