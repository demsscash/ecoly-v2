<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add control_number for multiple controls
     * DEFAULT 1 ensures existing single control system still works
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Add control_number with DEFAULT 1 (backward compatible)
            $table->integer('control_number')
                ->default(1)
                ->after('type')
                ->comment('Control sequence number (1, 2, 3...). Default 1 for single control.');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('control_number');
        });
    }
};
