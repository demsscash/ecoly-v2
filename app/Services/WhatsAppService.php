<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send attendance notification to guardian
     */
    public function sendAttendanceNotification(Attendance $attendance): bool
    {
        // Check if WhatsApp is enabled
        if (!config('services.whatsapp.enabled')) {
            Log::info('WhatsApp disabled, skipping notification');
            return false;
        }

        $student = $attendance->student;
        $guardianPhone = $student->guardian_phone;

        // Validate phone number
        if (empty($guardianPhone)) {
            Log::warning("No guardian phone for student {$student->id}");
            return false;
        }

        // Format phone number for WhatsApp (must include country code)
        $phoneNumber = $this->formatPhoneNumber($guardianPhone);

        // Build message
        $message = $this->buildMessage($attendance);

        try {
            // Send via Twilio WhatsApp API
            $response = Http::withBasicAuth(
                config('services.whatsapp.account_sid'),
                config('services.whatsapp.auth_token')
            )->asForm()->post(config('services.whatsapp.api_url'), [
                'From' => config('services.whatsapp.from_number'),
                'To' => 'whatsapp:' . $phoneNumber,
                'Body' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$phoneNumber} for student {$student->id}");
                return true;
            }

            Log::error("WhatsApp failed: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, parentheses
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // If doesn't start with +, assume Mauritania (+222)
        if (!str_starts_with($phone, '+')) {
            $phone = '+222' . $phone;
        }
        
        return $phone;
    }

    /**
     * Build notification message
     */
    private function buildMessage(Attendance $attendance): string
    {
        $student = $attendance->student;
        $school = \App\Models\SchoolSetting::first();
        
        $statusText = match($attendance->status) {
            'absent' => 'ABSENT(E)',
            'late' => 'EN RETARD',
            'left_early' => 'PARTI(E) AVANT LA FIN',
            default => strtoupper($attendance->getStatusLabel()),
        };

        $message = "🏫 " . ($school->name_fr ?? 'École') . "\n\n";
        $message .= "Bonjour " . $student->guardian_name . ",\n\n";
        $message .= "Votre enfant " . $student->full_name;
        $message .= " - Classe " . ($student->class->name ?? '-') . "\n";
        $message .= "a été marqué(e) " . $statusText . "\n";
        $message .= "le " . $attendance->date->format('d/m/Y') . ".\n\n";
        
        if ($school->phone) {
            $message .= "Pour toute question, contactez-nous au " . $school->phone . ".";
        }

        return $message;
    }
}
