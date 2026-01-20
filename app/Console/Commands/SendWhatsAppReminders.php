<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Services\BrevoService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReminders extends Command
{
    // Keeping signature same to avoid breaking schedule in console.php
    protected $signature = 'app:send-whatsapp-reminders';
    protected $description = 'Send Email reminders for book returns via Brevo';

    public function handle(BrevoService $brevo)
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
                if ($borrow->user->email) {
                    try {
                        $returnDate = Carbon::parse($borrow->return_date)->format('d M Y');
                        
                        $htmlContent = "
                            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                                <h2 style='color: #F59E0B;'>Pengingat Pengembalian Buku ({$rem['label']})</h2>
                                <p>Halo <strong>{$borrow->user->name}</strong>,</p>
                                <p>Jangan lupa untuk mengembalikan buku berikut:</p>
                                <ul style='list-style: none; padding: 0;'>
                                    <li><strong>Judul:</strong> {$borrow->book->title}</li>
                                    <li><strong>Batas Kembali:</strong> <span style='color: red;'>{$returnDate}</span></li>
                                </ul>
                                <p>Mohon kembalikan tepat waktu untuk menghindari denda.</p>
                                <p>Terima kasih, UNILAM Library.</p>
                            </div>
                        ";

                        $brevo->sendEmail(
                            $borrow->user->email, 
                            $borrow->user->name, 
                            "Pengingat Pengembalian: {$borrow->book->title}", 
                            $htmlContent
                        );

                        $this->info("Reminder sent to {$borrow->user->name}");
                        Log::info("Brevo Reminder Sent: To {$borrow->user->email}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send to {$borrow->user->name}");
                        Log::error("Brevo Reminder Failed: " . $e->getMessage());
                    }
                }
            }
        }
    }
}