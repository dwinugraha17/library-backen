<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiKey;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY');
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'chanddwi780@gmail.com');
        $this->fromName = env('MAIL_FROM_NAME', 'UNILAM Library');
    }

    public function sendEmail($toEmail, $toName, $subject, $htmlContent)
    {
        try {
            $apiKey = env('BREVO_API_KEY');
            $fromEmail = env('MAIL_FROM_ADDRESS', 'chanddwi780@gmail.com');
            $fromName = env('MAIL_FROM_NAME', 'UNILAM Library');

            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $fromName,
                    'email' => $fromEmail,
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $toName,
                    ],
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            Log::error('Brevo API Error: ' . $response->body());
            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Brevo Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
