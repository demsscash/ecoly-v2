<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teacher can be assigned to multiple classes for specific subjects
        // This is already handled by class_subject.teacher_id
        
        // Main class teacher (professeur principal)
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('main_teacher_id')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['main_teacher_id']);
            $table->dropColumn('main_teacher_id');
        });
    }
};
