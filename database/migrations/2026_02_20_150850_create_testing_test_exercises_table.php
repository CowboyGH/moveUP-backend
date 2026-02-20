<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testing_test_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('testing_id')->constrained()->onDelete('cascade');
            $table->foreignId('testing_exercise_id')->constrained()->onDelete('cascade');
            $table->integer('order_number')->default(0);
            $table->timestamps();

            $table->unique(['testing_id', 'testing_exercise_id'], 'testing_exercise_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_test_exercises');
    }
};
