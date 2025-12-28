<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->apiToken = config('services.whatsapp.api_token');
        $this->enabled = config('services.whatsapp.enabled', false);
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('WhatsApp disabled. Message not sent.', [
                'phone' => $phone,
                'message' => $message
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/send-message', [
                'phone' => $this->formatPhone($phone),
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phone
                ]);
                return true;
            }

            Log::error('WhatsApp API error', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 222 (Mauritania), add country code
        if (substr($phone, 0, 3) === '222') {
            return $phone;
        }
        
        // If starts with 0, replace with 222
        if (substr($phone, 0, 1) === '0') {
            return '222' . substr($phone, 1);
        }
        
        // Add 222 if no country code
        return '222' . $phone;
    }

    /**
     * Test the API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get($this->apiUrl . '/status');

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->successful() ? 'Connection successful' : 'Connection failed',
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
