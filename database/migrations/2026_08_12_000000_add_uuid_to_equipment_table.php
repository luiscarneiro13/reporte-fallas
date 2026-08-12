<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * uuid nullable a propósito: la tabla `equipment` ya tiene datos cargados en
 * producción y este hosting no permite backfill por comando artisan (sin
 * SSH/CLI). Se rellena vía endpoint HTTP manual (ver
 * EquipmentUuidBackfillController) y recién ahí puede pasar a NOT NULL en una
 * migración aparte. No se agrega qr_code_path: este proyecto no genera QR de
 * equipos (no tiene el paquete endroid/qr-code, a diferencia de ironflow).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
