<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add max_grade to class_subject for fondamental classes
     */
    public function up(): void
    {
        Schema::table('class_subject', function (Blueprint $table) {
            // Add max_grade column (nullable for now, fondamental classes will use it)
            $table->integer('max_grade')
                ->nullable()
                ->after('teacher_id')
                ->comment('Max grade for this subject in this class (fondamental only, college/lycee = 20)');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('class_subject', function (Blueprint $table) {
            $table->dropColumn('max_grade');
        });
    }
};
