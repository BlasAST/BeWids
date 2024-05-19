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
            $table->unsignedBigInteger('emisor');
            $table->unsignedBigInteger('receptor');
            $table->foreign('emisor')->references('id_usuario')->on('participantes');
            $table->foreign('receptor')->references('id_usuario')->on('participantes');
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
