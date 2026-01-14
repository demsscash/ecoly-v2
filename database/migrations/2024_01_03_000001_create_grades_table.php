<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('trimester_id')->constrained()->cascadeOnDelete();
            $table->decimal('control_grade', 5, 2)->nullable();
            $table->decimal('exam_grade', 5, 2)->nullable();
            $table->decimal('average', 5, 2)->nullable();
            $table->string('appreciation')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('entered_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'trimester_id']);
            $table->index(['class_id', 'trimester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
