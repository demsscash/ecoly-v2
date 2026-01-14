<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('old_control_grade', 5, 2)->nullable();
            $table->decimal('new_control_grade', 5, 2)->nullable();
            $table->decimal('old_exam_grade', 5, 2)->nullable();
            $table->decimal('new_exam_grade', 5, 2)->nullable();
            $table->decimal('old_average', 5, 2)->nullable();
            $table->decimal('new_average', 5, 2)->nullable();
            $table->string('old_appreciation')->nullable();
            $table->string('new_appreciation')->nullable();
            $table->string('action')->default('update'); // create, update, delete
            $table->timestamps();
            
            $table->index(['grade_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_histories');
    }
};
