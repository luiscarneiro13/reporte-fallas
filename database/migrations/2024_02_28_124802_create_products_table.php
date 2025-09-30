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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->string('name', 90)->index();
            $table->text('description')->nullable();
            $table->integer('available_qty')->default(0);
            $table->double('price')->default(0);

            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('type_article_id');

            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('type_article_id')->references('id')->on('type_articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
