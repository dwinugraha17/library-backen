<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Illuminate\Console\Command;

class TestFonnteApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-fonnte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Fonnte API connection';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnte)
    {
        $this->info('Testing Fonnte API connection...');

        $target = '6281224607822'; // nomor 
        $message = 'Test message: Sistem pengingat buku UNILAM Library berfungsi dengan baik.';

        $this->info("Sending test message to: {$target}");

        $response = $fonnte->sendMessage($target, $message);

        $this->info('Response received:');
        $this->line(var_export($response, true));

        if (isset($response['status']) && $response['status']) {
            $this->info('✅ WhatsApp message sent successfully!');
        } else {
            $this->error('❌ Failed to send WhatsApp message');
            $this->error('Reason: ' . json_encode($response));
        }
    }
}
