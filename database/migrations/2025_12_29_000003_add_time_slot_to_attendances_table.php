<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('time_slot_id')->nullable()->after('date')->constrained('time_slots')->onDelete('cascade');
            $table->foreignId('timetable_id')->nullable()->after('time_slot_id')->constrained('timetables')->onDelete('set null');
            
            // Modifier l'index unique pour inclure le créneau
            $table->dropUnique(['student_id', 'date']);
            $table->unique(['student_id', 'date', 'time_slot_id'], 'unique_student_date_slot');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['time_slot_id']);
            $table->dropForeign(['timetable_id']);
            $table->dropUnique('unique_student_date_slot');
            $table->dropColumn(['time_slot_id', 'timetable_id']);
            $table->unique(['student_id', 'date']);
        });
    }
};
