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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('sale_id');
            $table->string('name')->nullable();
            $table->string('rif')->nullable();
            $table->string('address')->nullable();
            $table->double('rate')->default(0);
            $table->double('average_rate')->default(0);
            $table->double('sub_total')->default(0);
            $table->double('tax')->default(0);
            $table->double('total')->default(0);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
