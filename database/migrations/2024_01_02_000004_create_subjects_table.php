<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->string('name_ar');
            $table->string('code')->unique(); // Ex: MATH, FR, AR
            $table->integer('coefficient')->default(1); // Weight for average (integer, not decimal)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for class-subject relationship
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('coefficient')->nullable()->default(1); // Override default coefficient
            $table->integer('max_grade')->nullable(); // Max grade for this subject (fondamental classes)
            $table->integer('grade_base')->nullable(); // Grade base (alternative to max_grade)
            $table->timestamps();

            $table->unique(['class_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject');
        Schema::dropIfExists('subjects');
    }
};
