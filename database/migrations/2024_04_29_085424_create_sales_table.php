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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('method_payment_id')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->double('rate')->default(0);
            $table->double('average_rate')->default(0);
            $table->double('total_items')->default(0);
            $table->double('total')->default(0);
            $table->boolean('service')->default(0);
            $table->boolean('paid')->default(0);
            $table->double('total_bs')->default(0);
            $table->timestamp('cancel_sale')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('method_payment_id')->references('id')->on('method_payments');
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
