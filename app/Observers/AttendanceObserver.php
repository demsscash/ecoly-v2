<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Jobs\SendAttendanceNotification;
use Illuminate\Support\Facades\Log;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        Log::info('Observer triggered: created', ['id' => $attendance->id, 'status' => $attendance->status]);
        
        if ($attendance->status === 'absent') {
            $this->sendAbsenceNotification($attendance);
        }
    }

    public function updated(Attendance $attendance): void
    {
        Log::info('Observer triggered: updated', ['id' => $attendance->id, 'status' => $attendance->status]);
        
        if ($attendance->isDirty('status') && $attendance->status === 'absent') {
            $this->sendAbsenceNotification($attendance);
        }
    }

    protected function sendAbsenceNotification(Attendance $attendance): void
    {
        $student = $attendance->student;
        
        if (!$student->guardian_phone && !$student->guardian_phone_2) {
            Log::warning('No phone for student', ['student_id' => $student->id]);
            return;
        }

        Log::info('Dispatching notification', ['student_id' => $student->id]);
        SendAttendanceNotification::dispatch($attendance);
    }
}
