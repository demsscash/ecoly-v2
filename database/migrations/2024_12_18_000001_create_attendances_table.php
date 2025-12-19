<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'justified', 'left_early'])
                ->default('present');
            $table->text('justification_note')->nullable();
            $table->string('justification_file')->nullable();
            $table->foreignId('marked_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // One record per student per day
            $table->unique(['student_id', 'date']);
            
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
