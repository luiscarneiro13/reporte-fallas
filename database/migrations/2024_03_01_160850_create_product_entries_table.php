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
        Schema::create('product_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->integer('entry_qty')->default(0);
            $table->double('purchase_price')->default(0);
            $table->double('selling_price')->default(0);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_entries');
    }
};
