<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWhatsAppReminders extends Command
{
    protected $signature = 'app:send-whatsapp-reminders';
    protected $description = 'Send WhatsApp reminders for book returns';

    public function handle(FonnteService $fonnte)
    {
        $reminders = [
            ['days' => 2, 'label' => 'H-2'],
            ['days' => 1, 'label' => 'H-1'],
        ];

        foreach ($reminders as $rem) {
            $targetDate = Carbon::today()->addDays($rem['days'])->toDateString();
            $borrowings = Borrowing::with(['user', 'book'])
                ->whereDate('return_date', $targetDate)
                ->where('status', 'borrowed')
                ->get();

            foreach ($borrowings as $borrow) {
                $message = "Halo {$borrow->user->name},\n\n" 
                    . "Ini adalah pengingat ({$rem['label']}) untuk mengembalikan buku:\n" 
                    . "*{$borrow->book->title}*\n\n" 
                    . "Batas waktu pengembalian adalah: " . Carbon::parse($borrow->return_date)->format('d M Y') . ".\n" 
                    . "Terima kasih telah menggunakan UNILAM Library.";

                $fonnte->sendMessage($borrow->user->phone_number, $message);
                $this->info("Reminder sent to {$borrow->user->name} for {$borrow->book->title}");
            }
        }
    }
}