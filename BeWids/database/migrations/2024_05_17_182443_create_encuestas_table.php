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
            $table->string('titulo');
            $table->string('votadores');
            $table->integer('numVotantes');
            $table->integer('porcetajeGanador');
            $table->integer('porcentajePerdedor');
            $table->timestamps();
            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');
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
