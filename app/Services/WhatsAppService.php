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
            $formattedPhone = $this->formatPhone($phone);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/send-message', [
                'to' => $formattedPhone,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $formattedPhone
                ]);
                return true;
            }

            // Handle rate limit (free trial)
            if ($response->status() === 429) {
                Log::warning('WhatsApp rate limit reached', [
                    'to' => $formattedPhone,
                    'response' => $response->json()
                ]);
                return false;
            }

            Log::error('WhatsApp API error', [
                'to' => $formattedPhone,
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

        // Add + prefix for international format
        if (substr($phone, 0, 1) !== '+') {
            // If starts with 222 (Mauritania), add +
            if (substr($phone, 0, 3) === '222') {
                return '+' . $phone;
            }

            // If starts with 0, replace with +222
            if (substr($phone, 0, 1) === '0') {
                return '+222' . substr($phone, 1);
            }

            // Add +222 if no country code
            return '+222' . $phone;
        }

        return $phone;
    }

    /**
     * Test the API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get($this->apiUrl . '/account');

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
