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
        Schema::create('equipment_project', function (Blueprint $table) {

            $table->id();

            // Clave foránea al modelo Equipment
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');

            // Clave foránea al modelo Project
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Clave primaria compuesta: Asegura que la combinación de IDs sea única.
            $table->unique(['equipment_id', 'project_id']);

            // Timestamps para saber cuándo se hizo la asignación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_project');
    }
};
