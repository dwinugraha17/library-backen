<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookReturnReminder;

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
                // WhatsApp Reminder
                $message = "Halo {$borrow->user->name},\n\n" 
                    . "Ini adalah pengingat ({$rem['label']}) untuk mengembalikan buku:\n" 
                    . "*{$borrow->book->title}*\n\n" 
                    . "Batas waktu pengembalian adalah: " . Carbon::parse($borrow->return_date)->format('d M Y') . ".\n" 
                    . "Terima kasih telah menggunakan UNILAM Library.";

                $response = $fonnte->sendMessage($borrow->user->phone_number, $message);
                
                if (isset($response['status']) && $response['status']) {
                    $this->info("Reminder sent to {$borrow->user->name} for {$borrow->book->title}");
                    \Illuminate\Support\Facades\Log::info("WA Reminder Sent: To {$borrow->user->name} ({$borrow->user->phone_number})");
                } else {
                    $this->error("Failed to send reminder to {$borrow->user->name}");
                    \Illuminate\Support\Facades\Log::error("WA Reminder Failed: To {$borrow->user->name}. Reason: " . json_encode($response));
                }

                // Email Reminder
                if ($borrow->user->email) {
                    try {
                        Mail::to($borrow->user->email)->send(new BookReturnReminder($borrow, $rem['label']));
                        $this->info("Email reminder sent to {$borrow->user->name} ({$borrow->user->email})");
                        \Illuminate\Support\Facades\Log::info("Email Reminder Sent: To {$borrow->user->email}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send email to {$borrow->user->name}");
                        \Illuminate\Support\Facades\Log::error("Email Reminder Failed: To {$borrow->user->email}. Reason: " . $e->getMessage());
                    }
                }
            }
        }
    }
}