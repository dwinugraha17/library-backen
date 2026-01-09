<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->baseUrl = config('services.fonnte.base_url', 'https://api.fonnte.com');
    }

    public function sendMessage($target, $message)
    {
        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl . '/send', [
                'target' => $target,
                'message' => $message,
            ]);

            // Log status code dan body untuk debugging
            \Illuminate\Support\Facades\Log::info('Fonnte API Response Status: ' . $response->status());
            \Illuminate\Support\Facades\Log::info('Fonnte API Response Body: ' . $response->body());

            // Cek apakah respons berisi body
            if ($response->body()) {
                $responseData = $response->json();

                // Cek apakah respons adalah JSON valid
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $responseData;
                } else {
                    \Illuminate\Support\Facades\Log::error('Fonnte Error: Invalid JSON response: ' . $response->body());
                    return ['status' => false, 'reason' => 'Invalid JSON response: ' . $response->body()];
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Fonnte Error: Empty response body');
                return ['status' => false, 'reason' => 'Empty response from server'];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fonnte Error: ' . $e->getMessage());
            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }
}
