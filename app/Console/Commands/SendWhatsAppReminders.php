<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookReturnReminder;

class SendWhatsAppReminders extends Command
{
    // Keeping signature same to avoid breaking schedule in console.php
    protected $signature = 'app:send-whatsapp-reminders';
    protected $description = 'Send Email reminders for book returns (WhatsApp Disabled)';

    public function handle()
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
                // Email Reminder Only
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