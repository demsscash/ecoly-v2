<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add main_teacher_id to classes
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('main_teacher_id')
                ->nullable()
                ->after('registration_fee')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['main_teacher_id']);
            $table->dropColumn('main_teacher_id');
        });
    }
};
