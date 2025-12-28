<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add level_type and serie_id to classes
     * DEFAULT 'fondamental' ensures NO REGRESSION for existing classes
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Add level_type with DEFAULT 'fondamental' (safe for existing data)
            $table->enum('level_type', ['fondamental', 'college', 'lycee'])
                ->default('fondamental')
                ->after('section');
            
            // Add serie_id (nullable, only for lycee 5/6/7)
            $table->foreignId('serie_id')
                ->nullable()
                ->after('level_type')
                ->constrained('series')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['serie_id']);
            $table->dropColumn(['level_type', 'serie_id']);
        });
    }
};
