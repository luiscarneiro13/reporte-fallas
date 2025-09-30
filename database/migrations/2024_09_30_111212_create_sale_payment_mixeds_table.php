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
        Schema::create('sale_payment_mixeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->decimal('bolivares_efectivo', 10, 2)->nullable();
            $table->decimal('dolares_efectivo', 10, 2)->nullable();
            $table->decimal('pago_movil', 10, 2)->nullable();
            $table->decimal('biopago', 10, 2)->nullable();
            $table->decimal('punto_venta_venezuela', 10, 2)->nullable();
            $table->decimal('punto_venta_banesco', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payment_mixeds');
    }
};
