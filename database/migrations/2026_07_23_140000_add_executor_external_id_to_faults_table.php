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
        Schema::table('faults', function (Blueprint $table) {
            $table->unsignedBigInteger('executor_external_id')->nullable()->after('executor_id');
            $table->foreign('executor_external_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faults', function (Blueprint $table) {
            $table->dropForeign(['executor_external_id']);
            $table->dropColumn('executor_external_id');
        });
    }
};
