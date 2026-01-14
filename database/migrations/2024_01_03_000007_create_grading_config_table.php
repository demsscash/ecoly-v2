<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            
            // Pondération contrôle/examen
            $table->integer('control_weight')->default(40); // Pourcentage
            $table->integer('exam_weight')->default(60); // Pourcentage
            
            // Seuils mentions (sur 20)
            $table->decimal('mention_excellent', 4, 2)->default(16.00);
            $table->decimal('mention_very_good', 4, 2)->default(14.00);
            $table->decimal('mention_good', 4, 2)->default(12.00);
            $table->decimal('mention_fairly_good', 4, 2)->default(10.00);
            
            // Seuil passage
            $table->decimal('passing_grade', 4, 2)->default(10.00);
            
            $table->timestamps();
            
            $table->unique('school_year_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_config');
    }
};
