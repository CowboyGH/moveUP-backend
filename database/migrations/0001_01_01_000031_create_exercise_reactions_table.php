<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_workout_id')->constrained()->onDelete('cascade');
            $table->enum('reaction', ['good', 'normal', 'bad']);
            $table->date('reaction_date');
            $table->timestamps();

            $table->unique(['user_id', 'exercise_id', 'reaction_date'], 'user_exercise_reaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_reactions');
    }
};
