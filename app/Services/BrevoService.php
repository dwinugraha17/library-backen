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
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail,
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
                return true;
            }

            Log::error('Brevo API Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Brevo Exception: ' . $e->getMessage());
            return false;
        }
    }
}
