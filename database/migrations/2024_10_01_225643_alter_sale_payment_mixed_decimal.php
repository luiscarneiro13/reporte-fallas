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
        Schema::table('sale_payment_mixeds', function (Blueprint $table) {
            $table->double('bolivares_efectivo')->default(0)->change();
            $table->double('dolares_efectivo')->default(0)->change();
            $table->double('pago_movil')->default(0)->change();
            $table->double('biopago')->default(0)->change();
            $table->double('punto_venta_venezuela')->default(0)->change();
            $table->double('punto_venta_banesco')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_payment_mixed', function (Blueprint $table) {
            //
        });
    }
};
