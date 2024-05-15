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
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_portal');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->boolean('admin')->default(false);
            $table->string('nombre_en_portal');
            $table->decimal('deuda')->default(0);
            $table->timestamps();

            $table->unique(['id_portal','nombre_en_portal']);
            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};
