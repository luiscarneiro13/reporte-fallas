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
        Schema::table('ficha_ingreso', function (Blueprint $table) {
            $table->date('driver_license_expiration_date')->nullable()->after('driver_license_grade');
            $table->date('occupational_certificate_expiration_date')->nullable()->after('has_occupational_certificate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ficha_ingreso', function (Blueprint $table) {
            $table->dropColumn(['driver_license_expiration_date', 'occupational_certificate_expiration_date']);
        });
    }
};
