<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->enum('reaction', ['bad', 'normal', 'good']);
            $table->integer('sets_completed')->nullable();
            $table->integer('reps_completed')->nullable();
            $table->decimal('weight_used', 8, 1)->nullable();
            $table->integer('sets_planned')->nullable();
            $table->integer('reps_planned')->nullable();
            $table->decimal('weight_planned', 8, 1)->nullable();
            $table->decimal('adjustment_factor', 5, 2)->default(1.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_performances');
    }
};
