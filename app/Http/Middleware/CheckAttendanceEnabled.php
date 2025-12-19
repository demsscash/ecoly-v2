<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckAttendanceEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settings = SchoolSetting::first();
        
        if (!$settings || !$settings->attendance_enabled) {
            abort(403, __('Attendance module is not enabled.'));
        }
        
        return $next($request);
    }
}
