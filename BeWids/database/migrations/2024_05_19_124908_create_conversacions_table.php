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
        Schema::create('conversacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_portal');
            $table->string('name_group')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('emisor');
            $table->string('receptor')->nullable();
            $table->json('participantes_group')->nullable();
            $table->boolean('chat_global')->default(false);
            $table->foreign('emisor')->references('nombre_en_portal')->on('participantes');
            $table->foreign('receptor')->references('nombre_en_portal')->on('participantes');
            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');
            $table->timestamp('ultimo_mensaje')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversacions');
    }
};
