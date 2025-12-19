<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAttendanceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Attendance $attendance
    ) {}

    /**
     * Execute the job
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        // Only send for problem statuses
        if (!$this->attendance->requiresNotification()) {
            return;
        }

        $whatsappService->sendAttendanceNotification($this->attendance);
    }
}
