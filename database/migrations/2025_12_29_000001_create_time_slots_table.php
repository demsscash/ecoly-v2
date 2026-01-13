<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "1ère heure", "2ème heure"
            $table->time('start_time'); // Ex: 08:00
            $table->time('end_time'); // Ex: 09:00
            $table->integer('order')->default(0); // Pour trier
            $table->boolean('is_break')->default(false); // Est-ce une récréation?
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
