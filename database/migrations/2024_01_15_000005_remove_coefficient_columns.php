<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove coefficient from subjects table
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'coefficient')) {
                $table->dropColumn('coefficient');
            }
        });

        // Remove coefficient from class_subject pivot table
        Schema::table('class_subject', function (Blueprint $table) {
            if (Schema::hasColumn('class_subject', 'coefficient')) {
                $table->dropColumn('coefficient');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->integer('coefficient')->default(1);
        });

        Schema::table('class_subject', function (Blueprint $table) {
            $table->integer('coefficient')->nullable();
        });
    }
};
