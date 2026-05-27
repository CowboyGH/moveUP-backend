<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('testings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('duration_minutes');
            $table->string('image')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });

        Schema::create('testing_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('testing_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('testing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('testing_test_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('testing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('testing_exercise_id')->constrained()->cascadeOnDelete();
            $table->integer('order_number')->default(0);
            $table->timestamps();

            $table->unique(['testing_id', 'testing_exercise_id'], 'testing_exercise_unique');
        });

        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('testing_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('pulse')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'completed_at']);
        });

        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('testing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('testing_exercise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->integer('result_value');
            $table->date('test_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('testing_test_exercises');
        Schema::dropIfExists('testing_categories');
        Schema::dropIfExists('testing_exercises');
        Schema::dropIfExists('testings');
        Schema::dropIfExists('categories');
    }
};
