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
            $table->unsignedBigInteger('id_portal');
            $table->string('emisor')->nullable();
            $table->string('receptor')->nullable();
            $table->json('participantes_group')->nullable();
            $table->unsignedBigInteger('conversacion_id')->nullable();


            $table->foreign('id_portal')->references('id')->on('portales')->onDelete('cascade');
            $table->foreign('conversacion_id')->references('id')->on('conversacions')->onDelete('cascade');

            $table->boolean('read')->default(0)->nullable();
            $table->json('body')->nullable();
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
