<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAbsenceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Attendance $attendance
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsapp): void
    {
        $student = $this->attendance->student;
        $date = $this->attendance->date->format('d/m/Y');
        
        // Build message in French
        $message = $this->buildMessage($student, $date);
        
        // Send to primary phone
        if ($student->guardian_phone) {
            $sent = $whatsapp->sendMessage($student->guardian_phone, $message);
            
            if ($sent) {
                Log::info('Absence notification sent', [
                    'student_id' => $student->id,
                    'phone' => $student->guardian_phone,
                    'date' => $date
                ]);
            }
        }
        
        // Send to secondary phone if exists
        if ($student->guardian_phone_2) {
            $whatsapp->sendMessage($student->guardian_phone_2, $message);
        }
    }

    /**
     * Build notification message
     */
    protected function buildMessage($student, string $date): string
    {
        $schoolName = \App\Models\SchoolSetting::first()?->name_fr ?? 'Ecole';
        
        return sprintf(
            "🔔 *Notification d'Absence*\n\n" .
            "Cher(e) parent,\n\n" .
            "Nous vous informons que votre enfant *%s* était absent(e) le *%s*.\n\n" .
            "Si cette absence était prévue, veuillez nous fournir une justification.\n\n" .
            "Cordialement,\n%s",
            $student->first_name . ' ' . $student->last_name,
            $date,
            $schoolName
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Absence notification failed', [
            'attendance_id' => $this->attendance->id,
            'student_id' => $this->attendance->student_id,
            'error' => $exception->getMessage()
        ]);
    }
}
