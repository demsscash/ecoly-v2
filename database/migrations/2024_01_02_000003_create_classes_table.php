<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Ex: "1ère A", "2ème B"
            $table->string('level'); // 1, 2, 3, 4, 5, 6
            $table->string('section')->nullable(); // A, B, C...
            $table->integer('grade_base')->default(10); // 10 or 20
            $table->integer('capacity')->default(40);
            $table->decimal('tuition_fee', 10, 2)->default(0); // Monthly fee
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['school_year_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
