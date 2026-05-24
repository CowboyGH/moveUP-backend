<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('duration_days');
            $table->integer('order_number');
            $table->timestamps();
        });

        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->constrained()->cascadeOnDelete();
            $table->integer('streak_days')->default(0);
            $table->integer('completed_workouts')->default(0);
            $table->integer('weekly_workout_goal')->default(4);
            $table->timestamps();
        });

        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('muscle_group');
            $table->timestamps();
        });

        Schema::create('warmups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->integer('duration_seconds')->default(60);
            $table->timestamps();
        });

        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('duration_minutes');
            $table->string('type')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });

        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->integer('sets');
            $table->integer('reps');
            $table->integer('order_number');
            $table->timestamps();

            $table->unique(['workout_id', 'exercise_id'], 'workout_exercise_unique');
        });

        Schema::create('workout_warmups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warmup_id')->constrained()->cascadeOnDelete();
            $table->integer('order_number');
            $table->timestamps();

            $table->unique(['workout_id', 'warmup_id'], 'workout_warmup_unique');
        });

        Schema::create('user_workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['assigned', 'started', 'completed'])->default('assigned');
            $table->timestamps();
        });

        Schema::create('exercise_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
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

        Schema::create('user_warmup_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warmup_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_workout_id')->constrained()->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });

        Schema::create('user_exercise_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight', 8, 1);
            $table->decimal('adjustment_factor', 5, 2)->default(1.0);
            $table->timestamps();

            $table->unique(['user_id', 'exercise_id']);
        });

        Schema::create('exercise_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_workout_id')->constrained()->cascadeOnDelete();
            $table->enum('reaction', ['good', 'normal', 'bad']);
            $table->date('reaction_date');
            $table->timestamps();

            $table->unique(['user_id', 'exercise_id', 'reaction_date'], 'user_exercise_reaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_reactions');
        Schema::dropIfExists('user_exercise_weights');
        Schema::dropIfExists('user_warmup_performances');
        Schema::dropIfExists('exercise_performances');
        Schema::dropIfExists('user_workouts');
        Schema::dropIfExists('workout_warmups');
        Schema::dropIfExists('workout_exercises');
        Schema::dropIfExists('workouts');
        Schema::dropIfExists('warmups');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('user_progress');
        Schema::dropIfExists('phases');
    }
};
