<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add coefficient to class_subject
     * DEFAULT 1 ensures NO IMPACT on fondamental grade calculations
     */
    public function up(): void
    {
        Schema::table('class_subject', function (Blueprint $table) {
            // Add coefficient with DEFAULT 1 (safe, no calculation change)
            $table->integer('coefficient')
                ->default(1)
                ->after('max_grade')
                ->comment('Used for college/lycee weighted averages');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('class_subject', function (Blueprint $table) {
            $table->dropColumn('coefficient');
        });
    }
};
