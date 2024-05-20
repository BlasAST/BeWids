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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('emisor');
            $table->foreign('emisor')->references('id_usuario')->on('participantes');
            $table->unsignedBigInteger('receptor');
            $table->foreign('receptor')->references('id_usuario')->on('participantes');
            $table->boolean('read')->default(0)->nullable();
            $table->text('body')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
