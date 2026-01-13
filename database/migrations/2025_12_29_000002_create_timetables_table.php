<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->string('room')->nullable(); // Salle de classe
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Une seule matière par créneau pour une classe
            $table->unique(['class_id', 'time_slot_id', 'day_of_week'], 'unique_class_slot_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
