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
        Schema::table('fault_history', function (Blueprint $table) {
            $table->unsignedBigInteger('executor_external_id')->nullable()->after('executor_id');
            $table->string('executor_external_name')->nullable()->after('executor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fault_history', function (Blueprint $table) {
            $table->dropColumn(['executor_external_id', 'executor_external_name']);
        });
    }
};
