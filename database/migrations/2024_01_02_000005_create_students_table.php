<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique(); // Student ID
            $table->string('first_name');
            $table->string('last_name');
            $table->string('first_name_ar')->nullable();
            $table->string('last_name_ar')->nullable();
            $table->date('birth_date');
            $table->string('birth_place')->nullable();
            $table->string('birth_place_ar')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('nationality')->default('Mauritanienne');
            
            // Parent/Guardian info
            $table->string('guardian_name');
            $table->string('guardian_name_ar')->nullable();
            $table->string('guardian_phone');
            $table->string('guardian_phone_2')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('guardian_profession')->nullable();
            $table->text('address')->nullable();
            $table->text('address_ar')->nullable();
            
            // School info
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->string('previous_school')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'transferred', 'graduated'])->default('active');
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            
            $table->timestamps();
            
            $table->index(['school_year_id', 'class_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
