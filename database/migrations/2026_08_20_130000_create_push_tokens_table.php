<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 512);
            // Nullable: el registro embebido en el login (App\Http\Controllers\Api\V1\AuthController)
            // no recibe la plataforma real del dispositivo, así que la deja en null en vez de
            // asumir un valor incorrecto (ver docs/api-endpoints-spec.md §6.4).
            $table->string('platform', 20)->nullable();
            $table->string('device_id', 191)->nullable();
            $table->string('app_version', 50)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('token');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
    }
};
