<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('fcm.table', 'users');

        Schema::table($table, function (Blueprint $table) {
            $table->string('fcm_token')->nullable();
        });
    }

    public function down(): void
    {
        $table = config('fcm.table', 'users');

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};