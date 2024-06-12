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
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_portal');
            $table->string('title');
            $table->text('descripcion')->nullable();
            $table->string('creador');
            $table->json('participantes');
            $table->json('votado_por')->nullable();
            $table->integer('num_votos_totales');
            $table->integer('num_votos_hechos')->default(0);
            $table->json('opciones_votos');
            $table->date('fecha_final')->nullable();
            $table->boolean('finalizada')->default(0);
            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
