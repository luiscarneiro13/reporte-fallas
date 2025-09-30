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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->text('product')->nullable();
            $table->integer('qty')->default(0);
            $table->double('price')->default(0);
            $table->double('price_bs')->default(0);
            $table->double('rate')->default(0);
            $table->double('average_rate')->default(0);
            $table->double('sub_total')->default(0);
            $table->double('sub_total_bs')->default(0);
            $table->unsignedBigInteger('product_service_id')->nullable();
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
